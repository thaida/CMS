/*! videojs-contrib-media-sources - v2.4.4 - 2016-01-22
* Copyright (c) 2016 Brightcove; Licensed  */
/**
 * mux.js
 *
 * Copyright (c) 2014 Brightcove
 * All rights reserved.
 *
 * A lightweight readable stream implemention that handles event dispatching.
 * Objects that inherit from streams should call init in their constructors.
 */
(function(window, muxjs, undefined) {
  var Stream = function() {
    this.init = function() {
      var listeners = {};
      /**
       * Add a listener for a specified event type.
       * @param type {string} the event name
       * @param listener {function} the callback to be invoked when an event of
       * the specified type occurs
       */
      this.on = function(type, listener) {
        if (!listeners[type]) {
          listeners[type] = [];
        }
        listeners[type].push(listener);
      };
      /**
       * Remove a listener for a specified event type.
       * @param type {string} the event name
       * @param listener {function} a function previously registered for this
       * type of event through `on`
       */
      this.off = function(type, listener) {
        var index;
        if (!listeners[type]) {
          return false;
        }
        index = listeners[type].indexOf(listener);
        listeners[type].splice(index, 1);
        return index > -1;
      };
      /**
       * Trigger an event of the specified type on this stream. Any additional
       * arguments to this function are passed as parameters to event listeners.
       * @param type {string} the event name
       */
      this.trigger = function(type) {
        var callbacks, i, length, args;
        callbacks = listeners[type];
        if (!callbacks) {
          return;
        }
        // Slicing the arguments on every invocation of this method
        // can add a significant amount of overhead. Avoid the
        // intermediate object creation for the common case of a
        // single callback argument
        if (arguments.length === 2) {
          length = callbacks.length;
          for (i = 0; i < length; ++i) {
            callbacks[i].call(this, arguments[1]);
          }
        } else {
          args = [];
          i = arguments.length;
          for (i = 1; i < arguments.length; ++i) {
            args.push(arguments[i])
          }
          length = callbacks.length;
          for (i = 0; i < length; ++i) {
            callbacks[i].apply(this, args);
          }
        }
      };
      /**
       * Destroys the stream and cleans up.
       */
      this.dispose = function() {
        listeners = {};
      };
    };
  };
  /**
   * Forwards all `data` events on this stream to the destination stream. The
   * destination stream should provide a method `push` to receive the data
   * events as they arrive.
   * @param destination {stream} the stream that will receive all `data` events
   * @param autoFlush {boolean} if false, we will not call `flush` on the destination
   *                            when the current stream emits a 'done' event
   * @see http://nodejs.org/api/stream.html#stream_readable_pipe_destination_options
   */
  Stream.prototype.pipe = function(destination) {
    this.on('data', function(data) {
      destination.push(data);
    });

    this.on('done', function() {
      destination.flush();
    });

    return destination;
  };

  // Default stream functions that are expected to be overridden to perform
  // actual work. These are provided by the prototype as a sort of no-op
  // implementation so that we don't have to check for their existence in the
  // `pipe` function above.
  Stream.prototype.push = function(data) {
    this.trigger('data', data);
  };
  Stream.prototype.flush = function() {
    this.trigger('done');
  };

  window.muxjs = window.muxjs || {};
  window.muxjs.utils = window.muxjs.utils || {};

  window.muxjs.utils.Stream = Stream;
})(this, this.muxjs);

(function(window, muxjs) {

var ExpGolomb;

/**
 * Parser for exponential Golomb codes, a variable-bitwidth number encoding
 * scheme used by h264.
 */
ExpGolomb = function(workingData) {
  var
    // the number of bytes left to examine in workingData
    workingBytesAvailable = workingData.byteLength,

    // the current word being examined
    workingWord = 0, // :uint

    // the number of bits left to examine in the current word
    workingBitsAvailable = 0; // :uint;

  // ():uint
  this.length = function() {
    return (8 * workingBytesAvailable);
  };

  // ():uint
  this.bitsAvailable = function() {
    return (8 * workingBytesAvailable) + workingBitsAvailable;
  };

  // ():void
  this.loadWord = function() {
    var
      position = workingData.byteLength - workingBytesAvailable,
      workingBytes = new Uint8Array(4),
      availableBytes = Math.min(4, workingBytesAvailable);

    if (availableBytes === 0) {
      throw new Error('no bytes available');
    }

    workingBytes.set(workingData.subarray(position,
                                          position + availableBytes));
    workingWord = new DataView(workingBytes.buffer).getUint32(0);

    // track the amount of workingData that has been processed
    workingBitsAvailable = availableBytes * 8;
    workingBytesAvailable -= availableBytes;
  };

  // (count:int):void
  this.skipBits = function(count) {
    var skipBytes; // :int
    if (workingBitsAvailable > count) {
      workingWord          <<= count;
      workingBitsAvailable -= count;
    } else {
      count -= workingBitsAvailable;
      skipBytes = Math.floor(count / 8);

      count -= (skipBytes * 8);
      workingBytesAvailable -= skipBytes;

      this.loadWord();

      workingWord <<= count;
      workingBitsAvailable -= count;
    }
  };

  // (size:int):uint
  this.readBits = function(size) {
    var
      bits = Math.min(workingBitsAvailable, size), // :uint
      valu = workingWord >>> (32 - bits); // :uint

    console.assert(size < 32, 'Cannot read more than 32 bits at a time');

    workingBitsAvailable -= bits;
    if (workingBitsAvailable > 0) {
      workingWord <<= bits;
    } else if (workingBytesAvailable > 0) {
      this.loadWord();
    }

    bits = size - bits;
    if (bits > 0) {
      return valu << bits | this.readBits(bits);
    } else {
      return valu;
    }
  };

  // ():uint
  this.skipLeadingZeros = function() {
    var leadingZeroCount; // :uint
    for (leadingZeroCount = 0 ; leadingZeroCount < workingBitsAvailable ; ++leadingZeroCount) {
      if (0 !== (workingWord & (0x80000000 >>> leadingZeroCount))) {
        // the first bit of working word is 1
        workingWord <<= leadingZeroCount;
        workingBitsAvailable -= leadingZeroCount;
        return leadingZeroCount;
      }
    }

    // we exhausted workingWord and still have not found a 1
    this.loadWord();
    return leadingZeroCount + this.skipLeadingZeros();
  };

  // ():void
  this.skipUnsignedExpGolomb = function() {
    this.skipBits(1 + this.skipLeadingZeros());
  };

  // ():void
  this.skipExpGolomb = function() {
    this.skipBits(1 + this.skipLeadingZeros());
  };

  // ():uint
  this.readUnsignedExpGolomb = function() {
    var clz = this.skipLeadingZeros(); // :uint
    return this.readBits(clz + 1) - 1;
  };

  // ():int
  this.readExpGolomb = function() {
    var valu = this.readUnsignedExpGolomb(); // :int
    if (0x01 & valu) {
      // the number is odd if the low order bit is set
      return (1 + valu) >>> 1; // add 1 to make it even, and divide by 2
    } else {
      return -1 * (valu >>> 1); // divide by two then make it negative
    }
  };

  // Some convenience functions
  // :Boolean
  this.readBoolean = function() {
    return 1 === this.readBits(1);
  };

  // ():int
  this.readUnsignedByte = function() {
    return this.readBits(8);
  };

  this.loadWord();
};

window.muxjs = muxjs || {};
muxjs.utils = muxjs.utils || {};

muxjs.utils.ExpGolomb = ExpGolomb;

})(this, this.muxjs);

(function(window, muxjs) {
'use strict';

var AacStream;

var
  ADTS_SAMPLING_FREQUENCIES = [
    96000,
    88200,
    64000,
    48000,
    44100,
    32000,
    24000,
    22050,
    16000,
    12000,
    11025,
    8000,
    7350
  ];

/*
 * Accepts a ElementaryStream and emits data events with parsed
 * AAC Audio Frames of the individual packets. Input audio in ADTS
 * format is unpacked and re-emitted as AAC frames.
 *
 * @see http://wiki.multimedia.cx/index.php?title=ADTS
 * @see http://wiki.multimedia.cx/?title=Understanding_AAC
 */
AacStream = function() {
  var self, buffer;

  AacStream.prototype.init.call(this);

  self = this;

  this.push = function(packet) {
    var
      i = 0,
      frameNum = 0,
      frameLength,
      protectionSkipBytes,
      frameEnd,
      oldBuffer,
      numFrames,
      sampleCount,
      aacFrameDuration;

    if (packet.type !== 'audio') {
      // ignore non-audio data
      return;
    }

    // Prepend any data in the buffer to the input data so that we can parse
    // aac frames the cross a PES packet boundary
    if (buffer) {
      oldBuffer = buffer;
      buffer = new Uint8Array(oldBuffer.byteLength + packet.data.byteLength);
      buffer.set(oldBuffer);
      buffer.set(packet.data, oldBuffer.byteLength);
    } else {
      buffer = packet.data;
    }

    // unpack any ADTS frames which have been fully received
    // for details on the ADTS header, see http://wiki.multimedia.cx/index.php?title=ADTS
    while (i + 5 < buffer.length) {

      // Loook for the start of an ADTS header..
      if (buffer[i] !== 0xFF || (buffer[i + 1] & 0xF6) !== 0xF0) {
        // If a valid header was not found,  jump one forward and attempt to
        // find a valid ADTS header starting at the next byte
        i++;
        continue;
      }

      // The protection skip bit tells us if we have 2 bytes of CRC data at the
      // end of the ADTS header
      protectionSkipBytes = (~buffer[i + 1] & 0x01) * 2;

      // Frame length is a 13 bit integer starting 16 bits from the
      // end of the sync sequence
      frameLength = ((buffer[i + 3] & 0x03) << 11) |
        (buffer[i + 4] << 3) |
        ((buffer[i + 5] & 0xe0) >> 5);

      sampleCount = ((buffer[i + 6] & 0x03) + 1) * 1024;
      aacFrameDuration = (sampleCount * 90000) /
        ADTS_SAMPLING_FREQUENCIES[(buffer[i + 2] & 0x3c) >>> 2];

      frameEnd = i + frameLength;

      // If we don't have enough data to actually finish this AAC frame, return
      // and wait for more data
      if (buffer.byteLength < frameEnd) {
        return;
      }

      // Otherwise, deliver the complete AAC frame
      this.trigger('data', {
        pts: packet.pts + (frameNum * aacFrameDuration),
        dts: packet.dts + (frameNum * aacFrameDuration),
        sampleCount: sampleCount,
        audioobjecttype: ((buffer[i + 2] >>> 6) & 0x03) + 1,
        channelcount: ((buffer[i + 2] & 1) << 3) |
          ((buffer[i + 3] & 0xc0) >>> 6),
        samplerate: ADTS_SAMPLING_FREQUENCIES[(buffer[i + 2] & 0x3c) >>> 2],
        samplingfrequencyindex: (buffer[i + 2] & 0x3c) >>> 2,
        // assume ISO/IEC 14496-12 AudioSampleEntry default of 16
        samplesize: 16,
        data: buffer.subarray(i + 7 + protectionSkipBytes, frameEnd)
      });

      // If the buffer is empty, clear it and return
      if (buffer.byteLength === frameEnd) {
        buffer = undefined;
        return;
      }

      frameNum++;

      // Remove the finished frame from the buffer and start the process again
      buffer = buffer.subarray(frameEnd);
    }
  };
};

AacStream.prototype = new muxjs.utils.Stream();

muxjs.codecs = muxjs.codecs || {};

muxjs.codecs.AacStream = AacStream;

})(this, this.muxjs);

(function(window, muxjs){
'use strict';

var H264Stream, NalByteStream;

/**
 * Accepts a NAL unit byte stream and unpacks the embedded NAL units.
 */
NalByteStream = function() {
  var
    syncPoint = 0,
    i,
    buffer;
  NalByteStream.prototype.init.call(this);

  this.push = function(data) {
    var swapBuffer;

    if (!buffer) {
      buffer = data.data;
    } else {
      swapBuffer = new Uint8Array(buffer.byteLength + data.data.byteLength);
      swapBuffer.set(buffer);
      swapBuffer.set(data.data, buffer.byteLength);
      buffer = swapBuffer;
    }

    // Rec. ITU-T H.264, Annex B
    // scan for NAL unit boundaries

    // a match looks like this:
    // 0 0 1 .. NAL .. 0 0 1
    // ^ sync point        ^ i
    // or this:
    // 0 0 1 .. NAL .. 0 0 0
    // ^ sync point        ^ i

    // advance the sync point to a NAL start, if necessary
    for (; syncPoint < buffer.byteLength - 3; syncPoint++) {
      if (buffer[syncPoint + 2] === 1) {
        // the sync point is properly aligned
        i = syncPoint + 5;
        break;
      }
    }

    while (i < buffer.byteLength) {
      // look at the current byte to determine if we've hit the end of
      // a NAL unit boundary
      switch (buffer[i]) {
      case 0:
        // skip past non-sync sequences
        if (buffer[i - 1] !== 0) {
          i += 2;
          break;
        } else if (buffer[i - 2] !== 0) {
          i++;
          break;
        }

        // deliver the NAL unit
        this.trigger('data', buffer.subarray(syncPoint + 3, i - 2));

        // drop trailing zeroes
        do {
          i++;
        } while (buffer[i] !== 1 && i < buffer.length);
        syncPoint = i - 2;
        i += 3;
        break;
      case 1:
        // skip past non-sync sequences
        if (buffer[i - 1] !== 0 ||
            buffer[i - 2] !== 0) {
          i += 3;
          break;
        }

        // deliver the NAL unit
        this.trigger('data', buffer.subarray(syncPoint + 3, i - 2));
        syncPoint = i - 2;
        i += 3;
        break;
      default:
        // the current byte isn't a one or zero, so it cannot be part
        // of a sync sequence
        i += 3;
        break;
      }
    }
    // filter out the NAL units that were delivered
    buffer = buffer.subarray(syncPoint);
    i -= syncPoint;
    syncPoint = 0;
  };

  this.flush = function() {
    // deliver the last buffered NAL unit
    if (buffer && buffer.byteLength > 3) {
      this.trigger('data', buffer.subarray(syncPoint + 3));
    }
    // reset the stream state
    buffer = null;
    syncPoint = 0;
    this.trigger('done');
  };
};
NalByteStream.prototype = new muxjs.utils.Stream();

/**
 * Accepts input from a ElementaryStream and produces H.264 NAL unit data
 * events.
 */
H264Stream = function() {
  var
    nalByteStream = new NalByteStream(),
    self,
    trackId,
    currentPts,
    currentDts,

    discardEmulationPreventionBytes,
    readSequenceParameterSet,
    skipScalingList;

  H264Stream.prototype.init.call(this);
  self = this;

  this.push = function(packet) {
    if (packet.type !== 'video') {
      return;
    }
    trackId = packet.trackId;
    currentPts = packet.pts;
    currentDts = packet.dts;

    nalByteStream.push(packet);
  };

  nalByteStream.on('data', function(data) {
    var
      event = {
        trackId: trackId,
        pts: currentPts,
        dts: currentDts,
        data: data
      };

    switch (data[0] & 0x1f) {
    case 0x05:
      event.nalUnitType = 'slice_layer_without_partitioning_rbsp_idr';
      break;
    case 0x06:
      event.nalUnitType = 'sei_rbsp';
      event.escapedRBSP = discardEmulationPreventionBytes(data.subarray(1));
      break;
    case 0x07:
      event.nalUnitType = 'seq_parameter_set_rbsp';
      event.escapedRBSP = discardEmulationPreventionBytes(data.subarray(1));
      event.config = readSequenceParameterSet(event.escapedRBSP);
      break;
    case 0x08:
      event.nalUnitType = 'pic_parameter_set_rbsp';
      break;
    case 0x09:
      event.nalUnitType = 'access_unit_delimiter_rbsp';
      break;

    default:
      break;
    }
    self.trigger('data', event);
  });
  nalByteStream.on('done', function() {
    self.trigger('done');
  });

  this.flush = function() {
    nalByteStream.flush();
  };

  /**
   * Advance the ExpGolomb decoder past a scaling list. The scaling
   * list is optionally transmitted as part of a sequence parameter
   * set and is not relevant to transmuxing.
   * @param count {number} the number of entries in this scaling list
   * @param expGolombDecoder {object} an ExpGolomb pointed to the
   * start of a scaling list
   * @see Recommendation ITU-T H.264, Section 7.3.2.1.1.1
   */
  skipScalingList = function(count, expGolombDecoder) {
    var
      lastScale = 8,
      nextScale = 8,
      j,
      deltaScale;

    for (j = 0; j < count; j++) {
      if (nextScale !== 0) {
        deltaScale = expGolombDecoder.readExpGolomb();
        nextScale = (lastScale + deltaScale + 256) % 256;
      }

      lastScale = (nextScale === 0) ? lastScale : nextScale;
    }
  };

  /**
   * Expunge any "Emulation Prevention" bytes from a "Raw Byte
   * Sequence Payload"
   * @param data {Uint8Array} the bytes of a RBSP from a NAL
   * unit
   * @return {Uint8Array} the RBSP without any Emulation
   * Prevention Bytes
   */
  discardEmulationPreventionBytes = function(data) {
    var
      length = data.byteLength,
      emulationPreventionBytesPositions = [],
      i = 1,
      newLength, newData;

    // Find all `Emulation Prevention Bytes`
    while (i < length - 2) {
      if (data[i] === 0 && data[i + 1] === 0 && data[i + 2] === 0x03) {
        emulationPreventionBytesPositions.push(i + 2);
        i += 2;
      } else {
        i++;
      }
    }

    // If no Emulation Prevention Bytes were found just return the original
    // array
    if (emulationPreventionBytesPositions.length === 0) {
      return data;
    }

    // Create a new array to hold the NAL unit data
    newLength = length - emulationPreventionBytesPositions.length;
    newData = new Uint8Array(newLength);
    var sourceIndex = 0;

    for (i = 0; i < newLength; sourceIndex++, i++) {
      if (sourceIndex === emulationPreventionBytesPositions[0]) {
        // Skip this byte
        sourceIndex++;
        // Remove this position index
        emulationPreventionBytesPositions.shift();
      }
      newData[i] = data[sourceIndex];
    }

    return newData;
  };

  /**
   * Read a sequence parameter set and return some interesting video
   * properties. A sequence parameter set is the H264 metadata that
   * describes the properties of upcoming video frames.
   * @param data {Uint8Array} the bytes of a sequence parameter set
   * @return {object} an object with configuration parsed from the
   * sequence parameter set, including the dimensions of the
   * associated video frames.
   */
  readSequenceParameterSet = function(data) {
    var
      frameCropLeftOffset = 0,
      frameCropRightOffset = 0,
      frameCropTopOffset = 0,
      frameCropBottomOffset = 0,
      expGolombDecoder, profileIdc, levelIdc, profileCompatibility,
      chromaFormatIdc, picOrderCntType,
      numRefFramesInPicOrderCntCycle, picWidthInMbsMinus1,
      picHeightInMapUnitsMinus1,
      frameMbsOnlyFlag,
      scalingListCount,
      i;

    expGolombDecoder = new muxjs.utils.ExpGolomb(data);
    profileIdc = expGolombDecoder.readUnsignedByte(); // profile_idc
    profileCompatibility = expGolombDecoder.readUnsignedByte(); // constraint_set[0-5]_flag
    levelIdc = expGolombDecoder.readUnsignedByte(); // level_idc u(8)
    expGolombDecoder.skipUnsignedExpGolomb(); // seq_parameter_set_id

    // some profiles have more optional data we don't need
    if (profileIdc === 100 ||
        profileIdc === 110 ||
        profileIdc === 122 ||
        profileIdc === 244 ||
        profileIdc ===  44 ||
        profileIdc ===  83 ||
        profileIdc ===  86 ||
        profileIdc === 118 ||
        profileIdc === 128 ||
        profileIdc === 138 ||
        profileIdc === 139 ||
        profileIdc === 134) {
      chromaFormatIdc = expGolombDecoder.readUnsignedExpGolomb();
      if (chromaFormatIdc === 3) {
        expGolombDecoder.skipBits(1); // separate_colour_plane_flag
      }
      expGolombDecoder.skipUnsignedExpGolomb(); // bit_depth_luma_minus8
      expGolombDecoder.skipUnsignedExpGolomb(); // bit_depth_chroma_minus8
      expGolombDecoder.skipBits(1); // qpprime_y_zero_transform_bypass_flag
      if (expGolombDecoder.readBoolean()) { // seq_scaling_matrix_present_flag
        scalingListCount = (chromaFormatIdc !== 3) ? 8 : 12;
        for (i = 0; i < scalingListCount; i++) {
          if (expGolombDecoder.readBoolean()) { // seq_scaling_list_present_flag[ i ]
            if (i < 6) {
              skipScalingList(16, expGolombDecoder);
            } else {
              skipScalingList(64, expGolombDecoder);
            }
          }
        }
      }
    }

    expGolombDecoder.skipUnsignedExpGolomb(); // log2_max_frame_num_minus4
    picOrderCntType = expGolombDecoder.readUnsignedExpGolomb();

    if (picOrderCntType === 0) {
      expGolombDecoder.readUnsignedExpGolomb(); //log2_max_pic_order_cnt_lsb_minus4
    } else if (picOrderCntType === 1) {
      expGolombDecoder.skipBits(1); // delta_pic_order_always_zero_flag
      expGolombDecoder.skipExpGolomb(); // offset_for_non_ref_pic
      expGolombDecoder.skipExpGolomb(); // offset_for_top_to_bottom_field
      numRefFramesInPicOrderCntCycle = expGolombDecoder.readUnsignedExpGolomb();
      for(i = 0; i < numRefFramesInPicOrderCntCycle; i++) {
        expGolombDecoder.skipExpGolomb(); // offset_for_ref_frame[ i ]
      }
    }

    expGolombDecoder.skipUnsignedExpGolomb(); // max_num_ref_frames
    expGolombDecoder.skipBits(1); // gaps_in_frame_num_value_allowed_flag

    picWidthInMbsMinus1 = expGolombDecoder.readUnsignedExpGolomb();
    picHeightInMapUnitsMinus1 = expGolombDecoder.readUnsignedExpGolomb();

    frameMbsOnlyFlag = expGolombDecoder.readBits(1);
    if (frameMbsOnlyFlag === 0) {
      expGolombDecoder.skipBits(1); // mb_adaptive_frame_field_flag
    }

    expGolombDecoder.skipBits(1); // direct_8x8_inference_flag
    if (expGolombDecoder.readBoolean()) { // frame_cropping_flag
      frameCropLeftOffset = expGolombDecoder.readUnsignedExpGolomb();
      frameCropRightOffset = expGolombDecoder.readUnsignedExpGolomb();
      frameCropTopOffset = expGolombDecoder.readUnsignedExpGolomb();
      frameCropBottomOffset = expGolombDecoder.readUnsignedExpGolomb();
    }

    return {
      profileIdc: profileIdc,
      levelIdc: levelIdc,
      profileCompatibility: profileCompatibility,
      width: ((picWidthInMbsMinus1 + 1) * 16) - frameCropLeftOffset * 2 - frameCropRightOffset * 2,
      height: ((2 - frameMbsOnlyFlag) * (picHeightInMapUnitsMinus1 + 1) * 16) - (frameCropTopOffset * 2) - (frameCropBottomOffset * 2)
    };
  };

};
H264Stream.prototype = new muxjs.utils.Stream();

muxjs.codecs = muxjs.codecs || {};

muxjs.codecs.H264Stream = H264Stream;
muxjs.codecs.NalByteStream = NalByteStream;

})(this, this.muxjs);

/**
 * mux.js
 *
 * Copyright (c) 2015 Brightcove
 * All rights reserved.
 *
 * A stream-based mp2t to mp4 converter. This utility can be used to
 * deliver mp4s to a SourceBuffer on platforms that support native
 * Media Source Extensions.
 */
(function(window, muxjs, undefined) {
'use strict';

// object types
var
  TransportPacketStream, TransportParseStream, ElementaryStream,
  AacStream, H264Stream, NalByteStream;

// constants
var
  MP2T_PACKET_LENGTH, H264_STREAM_TYPE, ADTS_STREAM_TYPE,
  METADATA_STREAM_TYPE, ADTS_SAMPLING_FREQUENCIES, SYNC_BYTE;

MP2T_PACKET_LENGTH = 188; // bytes
SYNC_BYTE = 0x47;

H264_STREAM_TYPE = 0x1b;
ADTS_STREAM_TYPE = 0x0f;
METADATA_STREAM_TYPE = 0x15;

/**
 * Splits an incoming stream of binary data into MPEG-2 Transport
 * Stream packets.
 */
TransportPacketStream = function() {
  var
    buffer = new Uint8Array(MP2T_PACKET_LENGTH),
    bytesInBuffer = 0;

  TransportPacketStream.prototype.init.call(this);

   // Deliver new bytes to the stream.

  this.push = function(bytes) {
    var
      i = 0,
      startIndex = 0,
      endIndex = MP2T_PACKET_LENGTH,
      everything;

    // If there are bytes remaining from the last segment, prepend them to the
    // bytes that were pushed in
    if (bytesInBuffer) {
      everything = new Uint8Array(bytes.byteLength + bytesInBuffer);
      everything.set(buffer.subarray(0, bytesInBuffer));
      everything.set(bytes, bytesInBuffer);
      bytesInBuffer = 0;
    } else {
      everything = bytes;
    }

    // While we have enough data for a packet
    while (endIndex < everything.byteLength) {
      // Look for a pair of start and end sync bytes in the data..
      if (everything[startIndex] === SYNC_BYTE && everything[endIndex] === SYNC_BYTE) {
        // We found a packet so emit it and jump one whole packet forward in
        // the stream
        this.trigger('data', everything.subarray(startIndex, endIndex));
        startIndex += MP2T_PACKET_LENGTH;
        endIndex += MP2T_PACKET_LENGTH;
        continue;
      }
      // If we get here, we have somehow become de-synchronized and we need to step
      // forward one byte at a time until we find a pair of sync bytes that denote
      // a packet
      startIndex++;
      endIndex++;
    }

    // If there was some data left over at the end of the segment that couldn't
    // possibly be a whole packet, keep it because it might be the start of a packet
    // that continues in the next segment
    if (startIndex < everything.byteLength) {
      buffer.set(everything.subarray(startIndex), 0);
      bytesInBuffer = everything.byteLength - startIndex;
    }
  };

  this.flush = function () {
    // If the buffer contains a whole packet when we are being flushed, emit it
    // and empty the buffer. Otherwise hold onto the data because it may be
    // important for decoding the next segment
    if (bytesInBuffer === MP2T_PACKET_LENGTH && buffer[0] === SYNC_BYTE) {
      this.trigger('data', buffer);
      bytesInBuffer = 0;
    }
    this.trigger('done');
  };
};
TransportPacketStream.prototype = new muxjs.utils.Stream();

/**
 * Accepts an MP2T TransportPacketStream and emits data events with parsed
 * forms of the individual transport stream packets.
 */
TransportParseStream = function() {
  var parsePsi, parsePat, parsePmt, parsePes, self;
  TransportParseStream.prototype.init.call(this);
  self = this;

  this.packetsWaitingForPmt = [];
  this.programMapTable = undefined;

  parsePsi = function(payload, psi) {
    var offset = 0;

    // PSI packets may be split into multiple sections and those
    // sections may be split into multiple packets. If a PSI
    // section starts in this packet, the payload_unit_start_indicator
    // will be true and the first byte of the payload will indicate
    // the offset from the current position to the start of the
    // section.
    if (psi.payloadUnitStartIndicator) {
      offset += payload[offset] + 1;
    }

    if (psi.type === 'pat') {
      parsePat(payload.subarray(offset), psi);
    } else {
      parsePmt(payload.subarray(offset), psi);
    }
  };

  parsePat = function(payload, pat) {
    pat.section_number = payload[7];
    pat.last_section_number = payload[8];

    // skip the PSI header and parse the first PMT entry
    self.pmtPid = (payload[10] & 0x1F) << 8 | payload[11];
    pat.pmtPid = self.pmtPid;
  };

  /**
   * Parse out the relevant fields of a Program Map Table (PMT).
   * @param payload {Uint8Array} the PMT-specific portion of an MP2T
   * packet. The first byte in this array should be the table_id
   * field.
   * @param pmt {object} the object that should be decorated with
   * fields parsed from the PMT.
   */
  parsePmt = function(payload, pmt) {
    var sectionLength, tableEnd, programInfoLength, offset;

    // PMTs can be sent ahead of the time when they should actually
    // take effect. We don't believe this should ever be the case
    // for HLS but we'll ignore "forward" PMT declarations if we see
    // them. Future PMT declarations have the current_next_indicator
    // set to zero.
    if (!(payload[5] & 0x01)) {
      return;
    }

    // overwrite any existing program map table
    self.programMapTable = {};

    // the mapping table ends at the end of the current section
    sectionLength = (payload[1] & 0x0f) << 8 | payload[2];
    tableEnd = 3 + sectionLength - 4;

    // to determine where the table is, we have to figure out how
    // long the program info descriptors are
    programInfoLength = (payload[10] & 0x0f) << 8 | payload[11];

    // advance the offset to the first entry in the mapping table
    offset = 12 + programInfoLength;
    while (offset < tableEnd) {
      // add an entry that maps the elementary_pid to the stream_type
      self.programMapTable[(payload[offset + 1] & 0x1F) << 8 | payload[offset + 2]] = payload[offset];

      // move to the next table entry
      // skip past the elementary stream descriptors, if present
      offset += ((payload[offset + 3] & 0x0F) << 8 | payload[offset + 4]) + 5;
    }

    // record the map on the packet as well
    pmt.programMapTable = self.programMapTable;

    // if there are any packets waiting for a PMT to be found, process them now
    while (self.packetsWaitingForPmt.length) {
      self.processPes_.apply(self, self.packetsWaitingForPmt.shift());
    }
  };

  /**
   * Deliver a new MP2T packet to the stream.
   */
  this.push = function(packet) {
    var
      result = {},
      offset = 4;

    result.payloadUnitStartIndicator = !!(packet[1] & 0x40);

    // pid is a 13-bit field starting at the last bit of packet[1]
    result.pid = packet[1] & 0x1f;
    result.pid <<= 8;
    result.pid |= packet[2];

    // if an adaption field is present, its length is specified by the
    // fifth byte of the TS packet header. The adaptation field is
    // used to add stuffing to PES packets that don't fill a complete
    // TS packet, and to specify some forms of timing and control data
    // that we do not currently use.
    if (((packet[3] & 0x30) >>> 4) > 0x01) {
      offset += packet[offset] + 1;
    }

    // parse the rest of the packet based on the type
    if (result.pid === 0) {
      result.type = 'pat';
      parsePsi(packet.subarray(offset), result);
      this.trigger('data', result);
    } else if (result.pid === this.pmtPid) {
      result.type = 'pmt';
      parsePsi(packet.subarray(offset), result);
      this.trigger('data', result);
    } else if (this.programMapTable === undefined) {
      this.packetsWaitingForPmt.push([packet, offset, result]);
    } else {
      this.processPes_(packet, offset, result);
    }
  };

  this.processPes_ = function (packet, offset, result) {
    result.streamType = this.programMapTable[result.pid];
    result.type = 'pes';
    result.data = packet.subarray(offset);

    this.trigger('data', result);
  };

};
TransportParseStream.prototype = new muxjs.utils.Stream();
TransportParseStream.STREAM_TYPES  = {
  h264: 0x1b,
  adts: 0x0f
};

/**
 * Reconsistutes program elementary stream (PES) packets from parsed
 * transport stream packets. That is, if you pipe an
 * mp2t.TransportParseStream into a mp2t.ElementaryStream, the output
 * events will be events which capture the bytes for individual PES
 * packets plus relevant metadata that has been extracted from the
 * container.
 */
ElementaryStream = function() {
  var
    // PES packet fragments
    video = {
      data: [],
      size: 0
    },
    audio = {
      data: [],
      size: 0
    },
    timedMetadata = {
      data: [],
      size: 0
    },
    parsePes = function(payload, pes) {
      var ptsDtsFlags;

      // find out if this packets starts a new keyframe
      pes.dataAlignmentIndicator = (payload[6] & 0x04) !== 0;
      // PES packets may be annotated with a PTS value, or a PTS value
      // and a DTS value. Determine what combination of values is
      // available to work with.
      ptsDtsFlags = payload[7];

      // PTS and DTS are normally stored as a 33-bit number.  Javascript
      // performs all bitwise operations on 32-bit integers but javascript
      // supports a much greater range (52-bits) of integer using standard
      // mathematical operations.
      // We construct a 31-bit value using bitwise operators over the 31
      // most significant bits and then multiply by 4 (equal to a left-shift
      // of 2) before we add the final 2 least significant bits of the
      // timestamp (equal to an OR.)
      if (ptsDtsFlags & 0xC0) {
        // the PTS and DTS are not written out directly. For information
        // on how they are encoded, see
        // http://dvd.sourceforge.net/dvdinfo/pes-hdr.html
        pes.pts = (payload[9] & 0x0E) << 27
          | (payload[10] & 0xFF) << 20
          | (payload[11] & 0xFE) << 12
          | (payload[12] & 0xFF) <<  5
          | (payload[13] & 0xFE) >>>  3;
        pes.pts *= 4; // Left shift by 2
        pes.pts += (payload[13] & 0x06) >>> 1; // OR by the two LSBs
        pes.dts = pes.pts;
        if (ptsDtsFlags & 0x40) {
          pes.dts = (payload[14] & 0x0E ) << 27
            | (payload[15] & 0xFF ) << 20
            | (payload[16] & 0xFE ) << 12
            | (payload[17] & 0xFF ) << 5
            | (payload[18] & 0xFE ) >>> 3;
          pes.dts *= 4; // Left shift by 2
          pes.dts += (payload[18] & 0x06) >>> 1; // OR by the two LSBs
        }
      }

      // the data section starts immediately after the PES header.
      // pes_header_data_length specifies the number of header bytes
      // that follow the last byte of the field.
      pes.data = payload.subarray(9 + payload[8]);
    },
    flushStream = function(stream, type) {
      var
        packetData = new Uint8Array(stream.size),
        event = {
          type: type
        },
        i = 0,
        fragment;

      // do nothing if there is no buffered data
      if (!stream.data.length) {
        return;
      }
      event.trackId = stream.data[0].pid;

      // reassemble the packet
      while (stream.data.length) {
        fragment = stream.data.shift();

        packetData.set(fragment.data, i);
        i += fragment.data.byteLength;
      }

      // parse assembled packet's PES header
      parsePes(packetData, event);

      stream.size = 0;

      self.trigger('data', event);
    },
    self;

  ElementaryStream.prototype.init.call(this);
  self = this;

  this.push = function(data) {
    ({
      pat: function() {
        // we have to wait for the PMT to arrive as well before we
        // have any meaningful metadata
      },
      pes: function() {
        var stream, streamType;

        switch (data.streamType) {
        case H264_STREAM_TYPE:
          stream = video;
          streamType = 'video';
          break;
        case ADTS_STREAM_TYPE:
          stream = audio;
          streamType = 'audio';
          break;
        case METADATA_STREAM_TYPE:
          stream = timedMetadata;
          streamType = 'timed-metadata';
          break;
        default:
          // ignore unknown stream types
          return;
        }

        // if a new packet is starting, we can flush the completed
        // packet
        if (data.payloadUnitStartIndicator) {
          flushStream(stream, streamType);
        }

        // buffer this fragment until we are sure we've received the
        // complete payload
        stream.data.push(data);
        stream.size += data.data.byteLength;
      },
      pmt: function() {
        var
          event = {
            type: 'metadata',
            tracks: []
          },
          programMapTable = data.programMapTable,
          k,
          track;

        // translate streams to tracks
        for (k in programMapTable) {
          if (programMapTable.hasOwnProperty(k)) {
            track = {
              timelineStartInfo: {
                baseMediaDecodeTime: 0
              }
            };
            track.id = +k;
            if (programMapTable[k] === H264_STREAM_TYPE) {
              track.codec = 'avc';
              track.type = 'video';
            } else if (programMapTable[k] === ADTS_STREAM_TYPE) {
              track.codec = 'adts';
              track.type = 'audio';
            }
            event.tracks.push(track);
          }
        }
        self.trigger('data', event);
      }
    })[data.type]();
  };

  /**
   * Flush any remaining input. Video PES packets may be of variable
   * length. Normally, the start of a new video packet can trigger the
   * finalization of the previous packet. That is not possible if no
   * more video is forthcoming, however. In that case, some other
   * mechanism (like the end of the file) has to be employed. When it is
   * clear that no additional data is forthcoming, calling this method
   * will flush the buffered packets.
   */
  this.flush = function() {
    // !!THIS ORDER IS IMPORTANT!!
    // video first then audio
    flushStream(video, 'video');
    flushStream(audio, 'audio');
    flushStream(timedMetadata, 'timed-metadata');
    this.trigger('done');
  };
};
ElementaryStream.prototype = new muxjs.utils.Stream();

// exports
muxjs.mp2t = muxjs.mp2t || {};

muxjs.mp2t.PAT_PID = 0x0000;
muxjs.mp2t.MP2T_PACKET_LENGTH = MP2T_PACKET_LENGTH;
muxjs.mp2t.H264_STREAM_TYPE = H264_STREAM_TYPE;
muxjs.mp2t.ADTS_STREAM_TYPE = ADTS_STREAM_TYPE;
muxjs.mp2t.METADATA_STREAM_TYPE = METADATA_STREAM_TYPE;

muxjs.mp2t.TransportPacketStream = TransportPacketStream;
muxjs.mp2t.TransportParseStream = TransportParseStream;
muxjs.mp2t.ElementaryStream = ElementaryStream;

})(this, this.muxjs);

/**
 * An object that stores the bytes of an FLV tag and methods for
 * querying and manipulating that data.
 * @see http://download.macromedia.com/f4v/video_file_format_spec_v10_1.pdf
 */
(function(window, muxjs) {
'use strict';

var FlvTag;

// (type:uint, extraData:Boolean = false) extends ByteArray
FlvTag = function(type, extraData) {
  var
    // Counter if this is a metadata tag, nal start marker if this is a video
    // tag. unused if this is an audio tag
    adHoc = 0, // :uint

    // The default size is 16kb but this is not enough to hold iframe
    // data and the resizing algorithm costs a bit so we create a larger
    // starting buffer for video tags
    bufferStartSize = 16384,

    // checks whether the FLV tag has enough capacity to accept the proposed
    // write and re-allocates the internal buffers if necessary
    prepareWrite = function(flv, count) {
      var
        bytes,
        minLength = flv.position + count;
      if (minLength < flv.bytes.byteLength) {
        // there's enough capacity so do nothing
        return;
      }

      // allocate a new buffer and copy over the data that will not be modified
      bytes = new Uint8Array(minLength * 2);
      bytes.set(flv.bytes.subarray(0, flv.position), 0);
      flv.bytes = bytes;
      flv.view = new DataView(flv.bytes.buffer);
    },

    // commonly used metadata properties
    widthBytes = FlvTag.widthBytes || new Uint8Array('width'.length),
    heightBytes = FlvTag.heightBytes || new Uint8Array('height'.length),
    videocodecidBytes = FlvTag.videocodecidBytes || new Uint8Array('videocodecid'.length),
    i;

  if (!FlvTag.widthBytes) {
    // calculating the bytes of common metadata names ahead of time makes the
    // corresponding writes faster because we don't have to loop over the
    // characters
    // re-test with test/perf.html if you're planning on changing this
    for (i = 0; i < 'width'.length; i++) {
      widthBytes[i] = 'width'.charCodeAt(i);
    }
    for (i = 0; i < 'height'.length; i++) {
      heightBytes[i] = 'height'.charCodeAt(i);
    }
    for (i = 0; i < 'videocodecid'.length; i++) {
      videocodecidBytes[i] = 'videocodecid'.charCodeAt(i);
    }

    FlvTag.widthBytes = widthBytes;
    FlvTag.heightBytes = heightBytes;
    FlvTag.videocodecidBytes = videocodecidBytes;
  }

  this.keyFrame = false; // :Boolean

  switch(type) {
  case FlvTag.VIDEO_TAG:
    this.length = 16;
    // Start the buffer at 256k
    bufferStartSize *= 6;
    break;
  case FlvTag.AUDIO_TAG:
    this.length = 13;
    this.keyFrame = true;
    break;
  case FlvTag.METADATA_TAG:
    this.length = 29;
    this.keyFrame = true;
    break;
  default:
    throw("Error Unknown TagType");
  }

  this.bytes = new Uint8Array(bufferStartSize);
  this.view = new DataView(this.bytes.buffer);
  this.bytes[0] = type;
  this.position = this.length;
  this.keyFrame = extraData; // Defaults to false

  // presentation timestamp
  this.pts = 0;
  // decoder timestamp
  this.dts = 0;

  // ByteArray#writeBytes(bytes:ByteArray, offset:uint = 0, length:uint = 0)
  this.writeBytes = function(bytes, offset, length) {
    var
      start = offset || 0,
      end;
    length = length || bytes.byteLength;
    end = start + length;

    prepareWrite(this, length);
    this.bytes.set(bytes.subarray(start, end), this.position);

    this.position += length;
    this.length = Math.max(this.length, this.position);
  };

  // ByteArray#writeByte(value:int):void
  this.writeByte = function(byte) {
    prepareWrite(this, 1);
    this.bytes[this.position] = byte;
    this.position++;
    this.length = Math.max(this.length, this.position);
  };

  // ByteArray#writeShort(value:int):void
  this.writeShort = function(short) {
    prepareWrite(this, 2);
    this.view.setUint16(this.position, short);
    this.position += 2;
    this.length = Math.max(this.length, this.position);
  };

  // Negative index into array
  // (pos:uint):int
  this.negIndex = function(pos) {
    return this.bytes[this.length - pos];
  };

  // The functions below ONLY work when this[0] == VIDEO_TAG.
  // We are not going to check for that because we dont want the overhead
  // (nal:ByteArray = null):int
  this.nalUnitSize = function() {
    if (adHoc === 0) {
      return 0;
    }

    return this.length - (adHoc + 4);
  };

  this.startNalUnit = function() {
    // remember position and add 4 bytes
    if (adHoc > 0) {
      throw new Error("Attempted to create new NAL wihout closing the old one");
    }

    // reserve 4 bytes for nal unit size
    adHoc = this.length;
    this.length += 4;
    this.position = this.length;
  };

  // (nal:ByteArray = null):void
  this.endNalUnit = function(nalContainer) {
    var
      nalStart, // :uint
      nalLength; // :uint

    // Rewind to the marker and write the size
    if (this.length === adHoc + 4) {
      // we started a nal unit, but didnt write one, so roll back the 4 byte size value
      this.length -= 4;
    } else if (adHoc > 0) {
      nalStart = adHoc + 4;
      nalLength = this.length - nalStart;

      this.position = adHoc;
      this.view.setUint32(this.position, nalLength);
      this.position = this.length;

      if (nalContainer) {
        // Add the tag to the NAL unit
        nalContainer.push(this.bytes.subarray(nalStart, nalStart + nalLength));
      }
    }

    adHoc = 0;
  };

  /**
   * Write out a 64-bit floating point valued metadata property. This method is
   * called frequently during a typical parse and needs to be fast.
   */
  // (key:String, val:Number):void
  this.writeMetaDataDouble = function(key, val) {
    var i;
    prepareWrite(this, 2 + key.length + 9);

    // write size of property name
    this.view.setUint16(this.position, key.length);
    this.position += 2;

    // this next part looks terrible but it improves parser throughput by
    // 10kB/s in my testing

    // write property name
    if (key === 'width') {
      this.bytes.set(widthBytes, this.position);
      this.position += 5;
    } else if (key === 'height') {
      this.bytes.set(heightBytes, this.position);
      this.position += 6;
    } else if (key === 'videocodecid') {
      this.bytes.set(videocodecidBytes, this.position);
      this.position += 12;
    } else {
      for (i = 0; i < key.length; i++) {
        this.bytes[this.position] = key.charCodeAt(i);
        this.position++;
      }
    }

    // skip null byte
    this.position++;

    // write property value
    this.view.setFloat64(this.position, val);
    this.position += 8;

    // update flv tag length
    this.length = Math.max(this.length, this.position);
    ++adHoc;
  };

  // (key:String, val:Boolean):void
  this.writeMetaDataBoolean = function(key, val) {
    var i;
    prepareWrite(this, 2);
    this.view.setUint16(this.position, key.length);
    this.position += 2;
    for (i = 0; i < key.length; i++) {
      console.assert(key.charCodeAt(i) < 255);
      prepareWrite(this, 1);
      this.bytes[this.position] = key.charCodeAt(i);
      this.position++;
    }
    prepareWrite(this, 2);
    this.view.setUint8(this.position, 0x01);
    this.position++;
    this.view.setUint8(this.position, val ? 0x01 : 0x00);
    this.position++;
    this.length = Math.max(this.length, this.position);
    ++adHoc;
  };

  // ():ByteArray
  this.finalize = function() {
    var
      dtsDelta, // :int
      len; // :int

    switch(this.bytes[0]) {
      // Video Data
    case FlvTag.VIDEO_TAG:
      this.bytes[11] = ((this.keyFrame || extraData) ? 0x10 : 0x20 ) | 0x07; // We only support AVC, 1 = key frame (for AVC, a seekable frame), 2 = inter frame (for AVC, a non-seekable frame)
      this.bytes[12] = extraData ?  0x00 : 0x01;

      dtsDelta = this.pts - this.dts;
      this.bytes[13] = (dtsDelta & 0x00FF0000) >>> 16;
      this.bytes[14] = (dtsDelta & 0x0000FF00) >>>  8;
      this.bytes[15] = (dtsDelta & 0x000000FF) >>>  0;
      break;

    case FlvTag.AUDIO_TAG:
      this.bytes[11] = 0xAF; // 44 kHz, 16-bit stereo
      this.bytes[12] = extraData ? 0x00 : 0x01;
      break;

    case FlvTag.METADATA_TAG:
      this.position = 11;
      this.view.setUint8(this.position, 0x02); // String type
      this.position++;
      this.view.setUint16(this.position, 0x0A); // 10 Bytes
      this.position += 2;
      // set "onMetaData"
      this.bytes.set([0x6f, 0x6e, 0x4d, 0x65,
                      0x74, 0x61, 0x44, 0x61,
                      0x74, 0x61], this.position);
      this.position += 10;
      this.bytes[this.position] = 0x08; // Array type
      this.position++;
      this.view.setUint32(this.position, adHoc);
      this.position = this.length;
      this.bytes.set([0, 0, 9], this.position);
      this.position += 3; // End Data Tag
      this.length = this.position;
      break;
    }

    len = this.length - 11;

    // write the DataSize field
    this.bytes[ 1] = (len & 0x00FF0000) >>> 16;
    this.bytes[ 2] = (len & 0x0000FF00) >>>  8;
    this.bytes[ 3] = (len & 0x000000FF) >>>  0;
    // write the Timestamp
    this.bytes[ 4] = (this.dts & 0x00FF0000) >>> 16;
    this.bytes[ 5] = (this.dts & 0x0000FF00) >>>  8;
    this.bytes[ 6] = (this.dts & 0x000000FF) >>>  0;
    this.bytes[ 7] = (this.dts & 0xFF000000) >>> 24;
    // write the StreamID
    this.bytes[ 8] = 0;
    this.bytes[ 9] = 0;
    this.bytes[10] = 0;

    // Sometimes we're at the end of the view and have one slot to write a
    // uint32, so, prepareWrite of count 4, since, view is uint8
    prepareWrite(this, 4);
    this.view.setUint32(this.length, this.length);
    this.length += 4;
    this.position += 4;

    // trim down the byte buffer to what is actually being used
    this.bytes = this.bytes.subarray(0, this.length);
    this.frameTime = FlvTag.frameTime(this.bytes);
    console.assert(this.bytes.byteLength === this.length);
    return this;
  };
};

FlvTag.AUDIO_TAG = 0x08; // == 8, :uint
FlvTag.VIDEO_TAG = 0x09; // == 9, :uint
FlvTag.METADATA_TAG = 0x12; // == 18, :uint

// (tag:ByteArray):Boolean {
FlvTag.isAudioFrame = function(tag) {
  return FlvTag.AUDIO_TAG === tag[0];
};

// (tag:ByteArray):Boolean {
FlvTag.isVideoFrame = function(tag) {
  return FlvTag.VIDEO_TAG === tag[0];
};

// (tag:ByteArray):Boolean {
FlvTag.isMetaData = function(tag) {
  return FlvTag.METADATA_TAG === tag[0];
};

// (tag:ByteArray):Boolean {
FlvTag.isKeyFrame = function(tag) {
  if (FlvTag.isVideoFrame(tag)) {
    return tag[11] === 0x17;
  }

  if (FlvTag.isAudioFrame(tag)) {
    return true;
  }

  if (FlvTag.isMetaData(tag)) {
    return true;
  }

  return false;
};

// (tag:ByteArray):uint {
FlvTag.frameTime = function(tag) {
  var pts = tag[ 4] << 16; // :uint
  pts |= tag[ 5] <<  8;
  pts |= tag[ 6] <<  0;
  pts |= tag[ 7] << 24;
  return pts;
};

muxjs.flv = muxjs.flv || {};
muxjs.flv.FlvTag = FlvTag;

})(this, this.muxjs);

/**
 * Accepts program elementary stream (PES) data events and parses out
 * ID3 metadata from them, if present.
 * @see http://id3.org/id3v2.3.0
 */
(function(window, muxjs, undefined) {
  'use strict';
  var
    // return a percent-encoded representation of the specified byte range
    // @see http://en.wikipedia.org/wiki/Percent-encoding
    percentEncode = function(bytes, start, end) {
      var i, result = '';
      for (i = start; i < end; i++) {
        result += '%' + ('00' + bytes[i].toString(16)).slice(-2);
      }
      return result;
    },
    // return the string representation of the specified byte range,
    // interpreted as UTf-8.
    parseUtf8 = function(bytes, start, end) {
      return window.decodeURIComponent(percentEncode(bytes, start, end));
    },
    // return the string representation of the specified byte range,
    // interpreted as ISO-8859-1.
    parseIso88591 = function(bytes, start, end) {
      return window.unescape(percentEncode(bytes, start, end));
    },
    parseSyncSafeInteger = function (data) {
      return (data[0] << 21) |
             (data[1] << 14) |
             (data[2] << 7) |
             (data[3]);
    },
    tagParsers = {
      'TXXX': function(tag) {
        var i;
        if (tag.data[0] !== 3) {
          // ignore frames with unrecognized character encodings
          return;
        }

        for (i = 1; i < tag.data.length; i++) {
          if (tag.data[i] === 0) {
            // parse the text fields
            tag.description = parseUtf8(tag.data, 1, i);
            // do not include the null terminator in the tag value
            tag.value = parseUtf8(tag.data, i + 1, tag.data.length - 1);
            break;
          }
        }
        tag.data = tag.value;
      },
      'WXXX': function(tag) {
        var i;
        if (tag.data[0] !== 3) {
          // ignore frames with unrecognized character encodings
          return;
        }

        for (i = 1; i < tag.data.length; i++) {
          if (tag.data[i] === 0) {
            // parse the description and URL fields
            tag.description = parseUtf8(tag.data, 1, i);
            tag.url = parseUtf8(tag.data, i + 1, tag.data.length);
            break;
          }
        }
      },
      'PRIV': function(tag) {
        var i;

        for (i = 0; i < tag.data.length; i++) {
          if (tag.data[i] === 0) {
            // parse the description and URL fields
            tag.owner = parseIso88591(tag.data, 0, i);
            break;
          }
        }
        tag.privateData = tag.data.subarray(i + 1);
        tag.data = tag.privateData;
      }
    },
    MetadataStream;

  MetadataStream = function(options) {
    var
      settings = {
        debug: !!(options && options.debug),

        // the bytes of the program-level descriptor field in MP2T
        // see ISO/IEC 13818-1:2013 (E), section 2.6 "Program and
        // program element descriptors"
        descriptor: options && options.descriptor
      },
      // the total size in bytes of the ID3 tag being parsed
      tagSize = 0,
      // tag data that is not complete enough to be parsed
      buffer = [],
      // the total number of bytes currently in the buffer
      bufferSize = 0,
      i;

    MetadataStream.prototype.init.call(this);

    // calculate the text track in-band metadata track dispatch type
    // https://html.spec.whatwg.org/multipage/embedded-content.html#steps-to-expose-a-media-resource-specific-text-track
    this.dispatchType = muxjs.mp2t.METADATA_STREAM_TYPE.toString(16);
    if (settings.descriptor) {
      for (i = 0; i < settings.descriptor.length; i++) {
        this.dispatchType += ('00' + settings.descriptor[i].toString(16)).slice(-2);
      }
    }

    this.push = function(chunk) {
      var tag, frameStart, frameSize, frame, i;

      if (chunk.type !== 'timed-metadata') {
        return;
      }

      // if data_alignment_indicator is set in the PES header,
      // we must have the start of a new ID3 tag. Assume anything
      // remaining in the buffer was malformed and throw it out
      if (chunk.dataAlignmentIndicator) {
        bufferSize = 0;
        buffer.length = 0;
      }

      // ignore events that don't look like ID3 data
      if (buffer.length === 0 &&
          (chunk.data.length < 10 ||
           chunk.data[0] !== 'I'.charCodeAt(0) ||
           chunk.data[1] !== 'D'.charCodeAt(0) ||
           chunk.data[2] !== '3'.charCodeAt(0))) {
        if (settings.debug) {
          console.log('Skipping unrecognized metadata packet');
        }
        return;
      }

      // add this chunk to the data we've collected so far
      buffer.push(chunk);
      bufferSize += chunk.data.byteLength;

      // grab the size of the entire frame from the ID3 header
      if (buffer.length === 1) {
        // the frame size is transmitted as a 28-bit integer in the
        // last four bytes of the ID3 header.
        // The most significant bit of each byte is dropped and the
        // results concatenated to recover the actual value.
        tagSize = parseSyncSafeInteger(chunk.data.subarray(6, 10));

        // ID3 reports the tag size excluding the header but it's more
        // convenient for our comparisons to include it
        tagSize += 10;
      }

      // if the entire frame has not arrived, wait for more data
      if (bufferSize < tagSize) {
        return;
      }

      // collect the entire frame so it can be parsed
      tag = {
        data: new Uint8Array(tagSize),
        frames: [],
        pts: buffer[0].pts,
        dts: buffer[0].dts
      };
      for (i = 0; i < tagSize;) {
        tag.data.set(buffer[0].data.subarray(0, tagSize - i), i);
        i += buffer[0].data.byteLength;
        bufferSize -= buffer[0].data.byteLength;
        buffer.shift();
      }

      // find the start of the first frame and the end of the tag
      frameStart = 10;
      if (tag.data[5] & 0x40) {
        // advance the frame start past the extended header
        frameStart += 4; // header size field
        frameStart += parseSyncSafeInteger(tag.data.subarray(10, 14));

        // clip any padding off the end
        tagSize -= parseSyncSafeInteger(tag.data.subarray(16, 20));
      }

      // parse one or more ID3 frames
      // http://id3.org/id3v2.3.0#ID3v2_frame_overview
      do {
        // determine the number of bytes in this frame
        frameSize = parseSyncSafeInteger(tag.data.subarray(frameStart + 4, frameStart + 8));
        if (frameSize < 1) {
          return console.log('Malformed ID3 frame encountered. Skipping metadata parsing.');
        }

        frame = {
          id: String.fromCharCode(tag.data[frameStart],
                                  tag.data[frameStart + 1],
                                  tag.data[frameStart + 2],
                                  tag.data[frameStart + 3]),
          data: tag.data.subarray(frameStart + 10, frameStart + frameSize + 10)
        };
        frame.key = frame.id;
        if (tagParsers[frame.id]) {
          tagParsers[frame.id](frame);
        }
        tag.frames.push(frame);

        frameStart += 10; // advance past the frame header
        frameStart += frameSize; // advance past the frame body
      } while (frameStart < tagSize);
      this.trigger('data', tag);
    };
  };
  MetadataStream.prototype = new muxjs.utils.Stream();

  // exports
  muxjs.mp2t = muxjs.mp2t || {};
  muxjs.mp2t.MetadataStream = MetadataStream;
})(this, this.muxjs);

/**
 * mux.js
 *
 * Copyright (c) 2015 Brightcove
 * All rights reserved.
 *
 * Reads in-band caption information from a video elementary
 * stream. Captions must follow the CEA-708 standard for injection
 * into an MPEG-2 transport streams.
 * @see https://en.wikipedia.org/wiki/CEA-708
 */
(function(window, muxjs, undefined) {
  'use strict';

  // -----------------
  // Link To Transport
  // -----------------

  // Supplemental enhancement information (SEI) NAL units have a
  // payload type field to indicate how they are to be
  // interpreted. CEAS-708 caption content is always transmitted with
  // payload type 0x04.
  var USER_DATA_REGISTERED_ITU_T_T35 = 4,
      RBSP_TRAILING_BITS = 128;

  /**
   * Parse a supplemental enhancement information (SEI) NAL unit.
   * Stops parsing once a message of type ITU T T35 has been found.
   *
   * @param bytes {Uint8Array} the bytes of a SEI NAL unit
   * @return {object} the parsed SEI payload
   * @see Rec. ITU-T H.264, 7.3.2.3.1
   */
  var parseSei = function(bytes) {
    var
      i = 0,
      result = {
        payloadType: -1,
        payloadSize: 0,
      },
      payloadType = 0,
      payloadSize = 0;

    // go through the sei_rbsp parsing each each individual sei_message
    while (i < bytes.byteLength) {
      // stop once we have hit the end of the sei_rbsp
      if (bytes[i] === RBSP_TRAILING_BITS) {
        break;
      }

      // Parse payload type
      while (bytes[i] === 0xFF) {
        payloadType += 255;
        i++;
      }
      payloadType += bytes[i++];

      // Parse payload size
      while (bytes[i] === 0xFF) {
        payloadSize += 255;
        i++;
      }
      payloadSize += bytes[i++];

      // this sei_message is a 608/708 caption so save it and break
      // there can only ever be one caption message in a frame's sei
      if (!result.payload && payloadType === USER_DATA_REGISTERED_ITU_T_T35) {
        result.payloadType = payloadType;
        result.payloadSize = payloadSize;
        result.payload = bytes.subarray(i, i + payloadSize);
        break;
      }

      // skip the payload and parse the next message
      i += payloadSize;
      payloadType = 0;
      payloadSize = 0;
    }

    return result;
  };

  // see ANSI/SCTE 128-1 (2013), section 8.1
  var parseUserData = function(sei) {
    // itu_t_t35_contry_code must be 181 (United States) for
    // captions
    if (sei.payload[0] !== 181) {
      return null;
    }

    // itu_t_t35_provider_code should be 49 (ATSC) for captions
    if (((sei.payload[1] << 8) | sei.payload[2]) !== 49) {
      return null;
    }

    // the user_identifier should be "GA94" to indicate ATSC1 data
    if (String.fromCharCode(sei.payload[3],
                            sei.payload[4],
                            sei.payload[5],
                            sei.payload[6]) !== 'GA94') {
      return null;
    }

    // finally, user_data_type_code should be 0x03 for caption data
    if (sei.payload[7] !== 0x03) {
      return null;
    }

    // return the user_data_type_structure and strip the trailing
    // marker bits
    return sei.payload.subarray(8, sei.payload.length - 1);
  };

  // see CEA-708-D, section 4.4
  var parseCaptionPackets = function(pts, userData) {
    var results = [], i, count, offset, data;

    // if this is just filler, return immediately
    if (!(userData[0] & 0x40)) {
      return results;
    }

    // parse out the cc_data_1 and cc_data_2 fields
    count = userData[0] & 0x1f;
    for (i = 0; i < count; i++) {
      offset = i * 3;
      data = {
        type: userData[offset + 2] & 0x03,
        pts: pts
      };

      // capture cc data when cc_valid is 1
      if (userData[offset + 2] & 0x04) {
        data.ccData = (userData[offset + 3] << 8) | userData[offset + 4];
        results.push(data);
      }
    }
    return results;
  };

  var CaptionStream = function() {
    var self = this;
    CaptionStream.prototype.init.call(this);

    this.captionPackets_ = [];

    this.field1_ = new Cea608Stream();

    // forward data and done events from field1_ to this CaptionStream
    this.field1_.on('data', this.trigger.bind(this, 'data'));
    this.field1_.on('done', this.trigger.bind(this, 'done'));
  };
  CaptionStream.prototype = new muxjs.utils.Stream();
  CaptionStream.prototype.push = function(event) {
    var sei, userData, captionPackets;

    // only examine SEI NALs
    if (event.nalUnitType !== 'sei_rbsp') {
      return;
    }

    // parse the sei
    sei = parseSei(event.escapedRBSP);

    // ignore everything but user_data_registered_itu_t_t35
    if (sei.payloadType !== USER_DATA_REGISTERED_ITU_T_T35) {
      return;
    }

    // parse out the user data payload
    userData = parseUserData(sei);

    // ignore unrecognized userData
    if (!userData) {
      return;
    }

    // parse out CC data packets and save them for later
    this.captionPackets_ = this.captionPackets_.concat(parseCaptionPackets(event.pts, userData));
  };

  CaptionStream.prototype.flush = function () {
    // make sure we actually parsed captions before proceeding
    if (!this.captionPackets_.length) {
      this.field1_.flush();
      return;
    }

    // sort caption byte-pairs based on their PTS values
    this.captionPackets_.sort(function(a, b) {
      return a.pts - b.pts;
    });

    // Push each caption into Cea608Stream
    this.captionPackets_.forEach(this.field1_.push, this.field1_);

    this.captionPackets_.length = 0;
    this.field1_.flush();
    return;
  };
  // ----------------------
  // Session to Application
  // ----------------------

  var BASIC_CHARACTER_TRANSLATION = {
    0x2a: 0xe1,
    0x5c: 0xe9,
    0x5e: 0xed,
    0x5f: 0xf3,
    0x60: 0xfa,
    0x7b: 0xe7,
    0x7c: 0xf7,
    0x7d: 0xd1,
    0x7e: 0xf1,
    0x7f: 0x2588
  };

  // Constants for the byte codes recognized by Cea608Stream. This
  // list is not exhaustive. For a more comprehensive listing and
  // semantics see
  // http://www.gpo.gov/fdsys/pkg/CFR-2010-title47-vol1/pdf/CFR-2010-title47-vol1-sec15-119.pdf
  var PADDING                    = 0x0000,

      // Pop-on Mode
      RESUME_CAPTION_LOADING     = 0x1420,
      END_OF_CAPTION             = 0x142f,

      // Roll-up Mode
      ROLL_UP_2_ROWS             = 0x1425,
      ROLL_UP_3_ROWS             = 0x1426,
      ROLL_UP_4_ROWS             = 0x1427,
      RESUME_DIRECT_CAPTIONING   = 0x1429,
      CARRIAGE_RETURN            = 0x142d,
      // Erasure
      BACKSPACE                  = 0x1421,
      ERASE_DISPLAYED_MEMORY     = 0x142c,
      ERASE_NON_DISPLAYED_MEMORY = 0x142e;

  // the index of the last row in a CEA-608 display buffer
  var BOTTOM_ROW = 14;
  // CEA-608 captions are rendered onto a 34x15 matrix of character
  // cells. The "bottom" row is the last element in the outer array.
  var createDisplayBuffer = function() {
    var result = [], i = BOTTOM_ROW + 1;
    while (i--) {
      result.push('');
    }
    return result;
  };

  var Cea608Stream = function() {
    Cea608Stream.prototype.init.call(this);

    this.mode_ = 'popOn';
    // When in roll-up mode, the index of the last row that will
    // actually display captions. If a caption is shifted to a row
    // with a lower index than this, it is cleared from the display
    // buffer
    this.topRow_ = 0;
    this.startPts_ = 0;
    this.displayed_ = createDisplayBuffer();
    this.nonDisplayed_ = createDisplayBuffer();
    this.lastControlCode_ = null;

    this.push = function(packet) {
      var data, swap, char0, char1;
      // remove the parity bits
      data = packet.ccData & 0x7f7f;

      // ignore duplicate control codes
      if (data === this.lastControlCode_) {
        this.lastControlCode_ = null;
        return;
      }

      // Store control codes
      if ((data & 0xf000) === 0x1000) {
        this.lastControlCode_ = data;
      } else {
        this.lastControlCode_ = null;
      }

      switch (data) {
      case PADDING:
        break;
      case RESUME_CAPTION_LOADING:
        this.mode_ = 'popOn';
        break;
      case END_OF_CAPTION:
        // if a caption was being displayed, it's gone now
        this.flushDisplayed(packet.pts);

        // flip memory
        swap = this.displayed_;
        this.displayed_ = this.nonDisplayed_;
        this.nonDisplayed_ = swap;

        // start measuring the time to display the caption
        this.startPts_ = packet.pts;
        break;

      case ROLL_UP_2_ROWS:
        this.topRow_ = BOTTOM_ROW - 1;
        this.mode_ = 'rollUp';
        break;
      case ROLL_UP_3_ROWS:
        this.topRow_ = BOTTOM_ROW - 2;
        this.mode_ = 'rollUp';
        break;
      case ROLL_UP_4_ROWS:
        this.topRow_ = BOTTOM_ROW - 3;
        this.mode_ = 'rollUp';
        break;
      case CARRIAGE_RETURN:
        this.flushDisplayed(packet.pts);
        this.shiftRowsUp_();
        this.startPts_ = packet.pts;
        break;

      case BACKSPACE:
        if (this.mode_ === 'popOn') {
          this.nonDisplayed_[BOTTOM_ROW] = this.nonDisplayed_[BOTTOM_ROW].slice(0, -1);
        } else {
          this.displayed_[BOTTOM_ROW] = this.displayed_[BOTTOM_ROW].slice(0, -1);
        }
        break;
      case ERASE_DISPLAYED_MEMORY:
        this.flushDisplayed(packet.pts);
        this.displayed_ = createDisplayBuffer();
        break;
      case ERASE_NON_DISPLAYED_MEMORY:
        this.nonDisplayed_ = createDisplayBuffer();
        break;
      default:
        char0 = data >>> 8;
        char1 = data & 0xff;

        // Look for a Channel 1 Preamble Address Code
        if (char0 >= 0x10 && char0 <= 0x17 &&
            char1 >= 0x40 && char1 <= 0x7F &&
            (char0 !== 0x10 || char1 < 0x60)) {
          // Follow Safari's lead and replace the PAC with a space
          char0 = char1 = 0x20;
        }

        // ignore unsupported control codes
        if ((char0 & 0xf0) === 0x10) {
          return;
        }

        // character handling is dependent on the current mode
        this[this.mode_](packet.pts, char0, char1);
        break;
      }
    };
  };
  Cea608Stream.prototype = new muxjs.utils.Stream();
  // Trigger a cue point that captures the current state of the
  // display buffer
  Cea608Stream.prototype.flushDisplayed = function(pts) {
    var row, i;

    for (i = 0; i < this.displayed_.length; i++) {
      row = this.displayed_[i];
      if (row.length) {
        this.trigger('data', {
          startPts: this.startPts_,
          endPts: pts,
          text: row
        });
      }
    }
  };

  // Mode Implementations
  Cea608Stream.prototype.popOn = function(pts, char0, char1) {
    var baseRow = this.nonDisplayed_[BOTTOM_ROW];

    // buffer characters
    char0 = BASIC_CHARACTER_TRANSLATION[char0] || char0;
    baseRow += String.fromCharCode(char0);

    char1 = BASIC_CHARACTER_TRANSLATION[char1] || char1;
    baseRow += String.fromCharCode(char1);
    this.nonDisplayed_[BOTTOM_ROW] = baseRow;
  };
  Cea608Stream.prototype.rollUp = function(pts, char0, char1) {
    var baseRow = this.displayed_[BOTTOM_ROW];
    if (baseRow === '') {
      // we're starting to buffer new display input, so flush out the
      // current display
      this.flushDisplayed(pts);

      this.startPts_ = pts;
    }

    char0 = BASIC_CHARACTER_TRANSLATION[char0] || char0;
    baseRow += String.fromCharCode(char0);

    char1 = BASIC_CHARACTER_TRANSLATION[char1] || char1;
    baseRow += String.fromCharCode(char1);
    this.displayed_[BOTTOM_ROW] = baseRow;
  };
  Cea608Stream.prototype.shiftRowsUp_ = function() {
    var i;
    // clear out inactive rows
    for (i = 0; i < this.topRow_; i++) {
      this.displayed_[i] = '';
    }
    // shift displayed rows up
    for (i = this.topRow_; i < BOTTOM_ROW; i++) {
      this.displayed_[i] = this.displayed_[i + 1];
    }
    // clear out the bottom row
    this.displayed_[BOTTOM_ROW] = '';
  };

  // exports
  muxjs.mp2t = muxjs.mp2t || {};
  muxjs.mp2t.CaptionStream = CaptionStream;
  muxjs.mp2t.Cea608Stream = Cea608Stream;

})(this, this.muxjs);

(function(window, muxjs) {
  'use strict';
  var
    FlvTag = muxjs.flv.FlvTag,
    MetadataStream = muxjs.MetadataStream,
    Transmuxer,
    VideoSegmentStream,
    AudioSegmentStream,
    CoalesceStream,
    collectTimelineInfo,
    metaDataTag,
    extraDataTag;

/**
 * Store information about the start and end of the tracka and the
 * duration for each frame/sample we process in order to calculate
 * the baseMediaDecodeTime
 */
collectTimelineInfo = function (track, data) {
  if (typeof data.pts === 'number') {
    if (track.timelineStartInfo.pts === undefined) {
      track.timelineStartInfo.pts = data.pts;
    } else {
      track.timelineStartInfo.pts =
        Math.min(track.timelineStartInfo.pts, data.pts);
    }
  }

  if (typeof data.dts === 'number') {
    if (track.timelineStartInfo.dts === undefined) {
      track.timelineStartInfo.dts = data.dts;
    } else {
      track.timelineStartInfo.dts =
        Math.min(track.timelineStartInfo.dts, data.dts);
    }
  }
};

metaDataTag = function(track, pts) {
  var
    tag = new FlvTag(FlvTag.METADATA_TAG); // :FlvTag

  tag.dts = pts;
  tag.pts = pts;

  tag.writeMetaDataDouble("videocodecid", 7);
  tag.writeMetaDataDouble("width", track.width);
  tag.writeMetaDataDouble("height", track.height);

  return tag;
};

extraDataTag = function(track, pts) {
  var
    i,
    tag = new FlvTag(FlvTag.VIDEO_TAG, true);

  tag.dts = pts;
  tag.pts = pts;

  tag.writeByte(0x01);// version
  tag.writeByte(track.profileIdc);// profile
  tag.writeByte(track.profileCompatibility);// compatibility
  tag.writeByte(track.levelIdc);// level
  tag.writeByte(0xFC | 0x03); // reserved (6 bits), NULA length size - 1 (2 bits)
  tag.writeByte(0xE0 | 0x01 ); // reserved (3 bits), num of SPS (5 bits)
  tag.writeShort( track.sps[0].length ); // data of SPS
  tag.writeBytes( track.sps[0] ); // SPS

  tag.writeByte(track.pps.length); // num of PPS (will there ever be more that 1 PPS?)
  for (i = 0 ; i < track.pps.length ; ++i) {
    tag.writeShort(track.pps[i].length); // 2 bytes for length of PPS
    tag.writeBytes(track.pps[i]); // data of PPS
  }

  return tag;
};

/**
 * Constructs a single-track, media segment from AAC data
 * events. The output of this stream can be fed to flash.
 */
AudioSegmentStream = function(track) {
  var
    aacFrames = [],
    aacFramesLength = 0,
    sequenceNumber = 0,
    earliestAllowedDts = 0,
    oldExtraData;

  AudioSegmentStream.prototype.init.call(this);

  this.push = function(data) {
    collectTimelineInfo(track, data);

    if (track && track.channelcount === undefined) {
      track.audioobjecttype = data.audioobjecttype;
      track.channelcount = data.channelcount;
      track.samplerate = data.samplerate;
      track.samplingfrequencyindex = data.samplingfrequencyindex;
      track.samplesize = data.samplesize;
      track.extraData = (track.audioobjecttype << 11) |
                        (track.samplingfrequencyindex << 7) |
                        (track.channelcount << 3);
    }

    data.pts = Math.round(data.pts / 90);
    data.dts = Math.round(data.dts / 90);

    // buffer audio data until end() is called
    aacFrames.push(data);
  };

  this.flush = function() {
    var currentFrame, aacFrame, deltaDts,lastMetaPts, tags = [];
    // return early if no audio data has been observed
    if (aacFrames.length === 0) {
      this.trigger('done');
      return;
    }

    lastMetaPts = -Infinity;

    while (aacFrames.length) {
      currentFrame = aacFrames.shift();

      // write out metadata tags every 1 second so that the decoder
      // is re-initialized quickly after seeking into a different
      // audio configuration
      if (track.extraData !== oldExtraData || currentFrame.pts - lastMetaPts >= 1000) {
        aacFrame = new FlvTag(FlvTag.METADATA_TAG);
        aacFrame.pts = currentFrame.pts;
        aacFrame.dts = currentFrame.dts;

        // AAC is always 10
        aacFrame.writeMetaDataDouble("audiocodecid", 10);
        aacFrame.writeMetaDataBoolean("stereo", 2 === track.channelcount);
        aacFrame.writeMetaDataDouble ("audiosamplerate", track.samplerate);
        // Is AAC always 16 bit?
        aacFrame.writeMetaDataDouble ("audiosamplesize", 16);

        tags.push(aacFrame);

        oldExtraData = track.extraData;

        aacFrame = new FlvTag(FlvTag.AUDIO_TAG, true);
        // For audio, DTS is always the same as PTS. We want to set the DTS
        // however so we can compare with video DTS to determine approximate
        // packet order
        aacFrame.pts = currentFrame.pts;
        aacFrame.dts = currentFrame.dts;

        aacFrame.view.setUint16(aacFrame.position, track.extraData);
        aacFrame.position += 2;
        aacFrame.length = Math.max(aacFrame.length, aacFrame.position);

        tags.push(aacFrame);

        lastMetaPts = currentFrame.pts;
      }
      aacFrame = new FlvTag(FlvTag.AUDIO_TAG);
      aacFrame.pts = currentFrame.pts;
      aacFrame.dts = currentFrame.dts;

      aacFrame.writeBytes(currentFrame.data);

      tags.push(aacFrame);
    }

    oldExtraData = null;
    this.trigger('data', {track: track, tags: tags});

    this.trigger('done');
  };
};
AudioSegmentStream.prototype = new muxjs.utils.Stream();

/**
 * Store FlvTags for the h264 stream
 * @param track {object} track metadata configuration
 */
VideoSegmentStream = function(track) {
  var
    sequenceNumber = 0,
    nalUnits = [],
    nalUnitsLength = 0,
    config,
    h264Frame;
  VideoSegmentStream.prototype.init.call(this);

  this.finishFrame = function(tags, frame) {
    if (frame) {
      // Check if keyframe and the length of tags.
      // This makes sure we write metadata on the first frame of a segment.
      if (track.newMetadata &&
          (frame.keyFrame || tags.length === 0)) {
        // Push extra data on every IDR frame in case we did a stream change + seek
        tags.push(metaDataTag(config, frame.pts));
        tags.push(extraDataTag(track, frame.pts));
        track.newMetadata = false;
      }

      frame.endNalUnit();
      tags.push(frame);
    }
  };

  this.push = function(data) {
    collectTimelineInfo(track, data);

    data.pts = Math.round(data.pts / 90);
    data.dts = Math.round(data.dts / 90);

    // buffer video until flush() is called
    nalUnits.push(data);
  };

  this.flush = function() {
    var
      currentNal,
      tags = [];

    // Throw away nalUnits at the start of the byte stream until we find
    // the first AUD
    while (nalUnits.length) {
      if (nalUnits[0].nalUnitType === 'access_unit_delimiter_rbsp') {
        break;
      }
      nalUnits.shift();
    }

    // return early if no video data has been observed
    if (nalUnits.length === 0) {
      this.trigger('done');
      return;
    }

    while (nalUnits.length) {
      currentNal = nalUnits.shift();

      // record the track config
      if (currentNal.nalUnitType === 'seq_parameter_set_rbsp') {
        track.newMetadata = true;
        config = currentNal.config;
        track.width = config.width;
        track.height = config.height;
        track.sps = [currentNal.data];
        track.profileIdc = config.profileIdc;
        track.levelIdc = config.levelIdc;
        track.profileCompatibility = config.profileCompatibility;
        h264Frame.endNalUnit();
      } else if (currentNal.nalUnitType === 'pic_parameter_set_rbsp') {
        track.newMetadata = true;
        track.pps = [currentNal.data];
        h264Frame.endNalUnit();
      } else if (currentNal.nalUnitType === 'access_unit_delimiter_rbsp') {
        if (h264Frame) {
          this.finishFrame(tags, h264Frame);
        }
        h264Frame = new FlvTag(FlvTag.VIDEO_TAG);
        h264Frame.pts = currentNal.pts;
        h264Frame.dts = currentNal.dts;
      } else {
        if (currentNal.nalUnitType === 'slice_layer_without_partitioning_rbsp_idr') {
          // the current sample is a key frame
          h264Frame.keyFrame = true;
        }
        h264Frame.endNalUnit();
      }
      h264Frame.startNalUnit();
      h264Frame.writeBytes(currentNal.data);
    }
    if (h264Frame) {
      this.finishFrame(tags, h264Frame);
    }

    this.trigger('data', {track: track, tags: tags});

    // Continue with the flush process now
    this.trigger('done');
  };
};

VideoSegmentStream.prototype = new muxjs.utils.Stream();

/**
 * The final stage of the transmuxer that emits the flv tags
 * for audio, video, and metadata. Also tranlates in time and
 * outputs caption data and id3 cues.
 */
CoalesceStream = function(options) {
  // Number of Tracks per output segment
  // If greater than 1, we combine multiple
  // tracks into a single segment
  this.numberOfTracks = 0;
  this.metadataStream = options.metadataStream;

  this.videoTags = [];
  this.audioTags = [];
  this.videoTrack = null;
  this.audioTrack = null;
  this.pendingCaptions = [];
  this.pendingMetadata = [];
  this.pendingTracks = 0;

  CoalesceStream.prototype.init.call(this);

  // Take output from multiple
  this.push = function(output) {
    // buffer incoming captions until the associated video segment
    // finishes
    if (output.text) {
      return this.pendingCaptions.push(output);
    }
    // buffer incoming id3 tags until the final flush
    if (output.frames) {
      return this.pendingMetadata.push(output);
    }

    if (output.track.type === 'video') {
      this.videoTrack = output.track;
      this.videoTags = output.tags;
      this.pendingTracks++;
    }
    if (output.track.type === 'audio') {
      this.audioTrack = output.track;
      this.audioTags = output.tags;
      this.pendingTracks++;
    }
  };
};

CoalesceStream.prototype = new muxjs.utils.Stream();
CoalesceStream.prototype.flush = function() {
  var
    id3,
    caption,
    i,
    timelineStartPts,
    event = {
      tags: {},
      captions: [],
      metadata: []
    };

  if (this.pendingTracks < this.numberOfTracks) {
    return;
  }

  if (this.videoTrack) {
    timelineStartPts = this.videoTrack.timelineStartInfo.pts;
  } else if (this.audioTrack) {
    timelineStartPts = this.audioTrack.timelineStartInfo.pts;
  }

  event.tags.videoTags = this.videoTags;
  event.tags.audioTags = this.audioTags;

  // Translate caption PTS times into second offsets into the
  // video timeline for the segment
  for (i = 0; i < this.pendingCaptions.length; i++) {
    caption = this.pendingCaptions[i];
    caption.startTime = caption.startPts - timelineStartPts;
    caption.startTime /= 90e3;
    caption.endTime = caption.endPts - timelineStartPts;
    caption.endTime /= 90e3;
    event.captions.push(caption);
  }

  // Translate ID3 frame PTS times into second offsets into the
  // video timeline for the segment
  for (i = 0; i < this.pendingMetadata.length; i++) {
    id3 = this.pendingMetadata[i];
    id3.cueTime = id3.pts - timelineStartPts;
    id3.cueTime /= 90e3;
    event.metadata.push(id3);
  }
  // We add this to every single emitted segment even though we only need
  // it for the first
  event.metadata.dispatchType = this.metadataStream.dispatchType;

  // Reset stream state
  this.videoTrack = null;
  this.audioTrack = null;
  this.videoTags = [];
  this.audioTags = [];
  this.pendingCaptions.length = 0;
  this.pendingMetadata.length = 0;
  this.pendingTracks = 0;

  // Emit the final segment
  this.trigger('data', event);

  this.trigger('done');
};

/**
 * An object that incrementally transmuxes MPEG2 Trasport Stream
 * chunks into an FLV.
 */
Transmuxer = function(options) {
  var
    self = this,
    videoTrack,
    audioTrack,

    packetStream, parseStream, elementaryStream,
    aacStream, h264Stream,
    videoSegmentStream, audioSegmentStream, captionStream,
    coalesceStream;

  Transmuxer.prototype.init.call(this);

  options = options || {};

  // expose the metadata stream
  this.metadataStream = new muxjs.mp2t.MetadataStream();

  options.metadataStream = this.metadataStream;

  // set up the parsing pipeline
  packetStream = new muxjs.mp2t.TransportPacketStream();
  parseStream = new muxjs.mp2t.TransportParseStream();
  elementaryStream = new muxjs.mp2t.ElementaryStream();
  aacStream = new muxjs.codecs.AacStream();
  h264Stream = new muxjs.codecs.H264Stream();
  coalesceStream = new CoalesceStream(options);

  // disassemble MPEG2-TS packets into elementary streams
  packetStream
    .pipe(parseStream)
    .pipe(elementaryStream);

  // !!THIS ORDER IS IMPORTANT!!
  // demux the streams
  elementaryStream
    .pipe(h264Stream);
  elementaryStream
    .pipe(aacStream);

  elementaryStream
    .pipe(this.metadataStream)
    .pipe(coalesceStream);
  // if CEA-708 parsing is available, hook up a caption stream
  if (muxjs.mp2t.CaptionStream) {
    captionStream = new muxjs.mp2t.CaptionStream();
    h264Stream.pipe(captionStream)
      .pipe(coalesceStream);
  }

  // hook up the segment streams once track metadata is delivered
  elementaryStream.on('data', function(data) {
    var i, videoTrack, audioTrack;

    if (data.type === 'metadata') {
      i = data.tracks.length;

      // scan the tracks listed in the metadata
      while (i--) {
        if (data.tracks[i].type === 'video') {
          videoTrack = data.tracks[i];
        } else if (data.tracks[i].type === 'audio') {
          audioTrack = data.tracks[i];
        }
      }

      // hook up the video segment stream to the first track with h264 data
      if (videoTrack && !videoSegmentStream) {
        coalesceStream.numberOfTracks++;
        videoSegmentStream = new VideoSegmentStream(videoTrack);

        // Set up the final part of the video pipeline
        h264Stream
          .pipe(videoSegmentStream)
          .pipe(coalesceStream);
      }

      if (audioTrack && !audioSegmentStream) {
        // hook up the audio segment stream to the first track with aac data
        coalesceStream.numberOfTracks++;
        audioSegmentStream = new AudioSegmentStream(audioTrack);

        // Set up the final part of the audio pipeline
        aacStream
          .pipe(audioSegmentStream)
          .pipe(coalesceStream);
      }
    }
  });

  // feed incoming data to the front of the parsing pipeline
  this.push = function(data) {
    packetStream.push(data);
  };

  // flush any buffered data
  this.flush = function() {
    // Start at the top of the pipeline and flush all pending work
    packetStream.flush();
  };

  // Re-emit any data coming from the coalesce stream to the outside world
  coalesceStream.on('data', function (event) {
    self.trigger('data', event);
  });

  // Let the consumer know we have finished flushing the entire pipeline
  coalesceStream.on('done', function () {
    self.trigger('done');
  });

  // For information on the FLV format, see
  // http://download.macromedia.com/f4v/video_file_format_spec_v10_1.pdf.
  // Technically, this function returns the header and a metadata FLV tag
  // if duration is greater than zero
  // duration in seconds
  // @return {object} the bytes of the FLV header as a Uint8Array
  this.getFlvHeader = function(duration, audio, video) { // :ByteArray {
    var
      headBytes = new Uint8Array(3 + 1 + 1 + 4),
      head = new DataView(headBytes.buffer),
      metadata,
      result,
      metadataLength;

    // default arguments
    duration = duration || 0;
    audio = audio === undefined? true : audio;
    video = video === undefined? true : video;

    // signature
    head.setUint8(0, 0x46); // 'F'
    head.setUint8(1, 0x4c); // 'L'
    head.setUint8(2, 0x56); // 'V'

    // version
    head.setUint8(3, 0x01);

    // flags
    head.setUint8(4, (audio ? 0x04 : 0x00) | (video ? 0x01 : 0x00));

    // data offset, should be 9 for FLV v1
    head.setUint32(5, headBytes.byteLength);

    // init the first FLV tag
    if (duration <= 0) {
      // no duration available so just write the first field of the first
      // FLV tag
      result = new Uint8Array(headBytes.byteLength + 4);
      result.set(headBytes);
      result.set([0, 0, 0, 0], headBytes.byteLength);
      return result;
    }

    // write out the duration metadata tag
    metadata = new FlvTag(FlvTag.METADATA_TAG);
    metadata.pts = metadata.dts = 0;
    metadata.writeMetaDataDouble("duration", duration);
    metadataLength = metadata.finalize().length;
    result = new Uint8Array(headBytes.byteLength + metadataLength);
    result.set(headBytes);
    result.set(head.byteLength, metadataLength);

    return result;
  };
};
Transmuxer.prototype = new muxjs.utils.Stream();

// forward compatibility
muxjs.flv = muxjs.flv || {};
muxjs.flv.Transmuxer = Transmuxer;

})(this, this.muxjs);

(function(window, muxjs, undefined){
  'use strict';
  var urlCount = 0,
      EventTarget = videojs.EventTarget,
      defaults,
      VirtualSourceBuffer,
      flvCodec = /video\/flv(;\s*codecs=["']vp6,aac["'])?$/,
      objectUrlPrefix = 'blob:vjs-media-source/',
      interceptBufferCreation,
      aggregateUpdateHandler,
      scheduleTick,
      Cue,
      deprecateOldCue,
      removeCuesFromTrack,
      createTextTracksIfNecessary,
      addTextTrackData;

deprecateOldCue = function(cue) {
  Object.defineProperties(cue.frame, {
    'id': {
      get: function() {
        videojs.log.warn('cue.frame.id is deprecated. Use cue.value.key instead.');
        return cue.value.key;
      }
    },
    'value': {
      get: function() {
        videojs.log.warn('cue.frame.value is deprecated. Use cue.value.data instead.');
        return cue.value.data;
      }
    },
    'privateData': {
      get: function() {
        videojs.log.warn('cue.frame.privateData is deprecated. Use cue.value.data instead.');
        return cue.value.data;
      }
    }
  });
};

removeCuesFromTrack = function(start, end, track) {
  var i, cue;

  if (!track) {
    return;
  }

  i = track.cues.length;

  while(i--) {
    cue = track.cues[i];

    // Remove any overlapping cue
    if (cue.startTime <= end && cue.endTime >= start) {
      track.removeCue(cue);
    }
  }
};

createTextTracksIfNecessary = function (sourceBuffer, mediaSource, segment) {
  // create an in-band caption track if one is present in the segment
  if (segment.captions &&
      segment.captions.length &&
      !sourceBuffer.inbandTextTrack_) {
    sourceBuffer.inbandTextTrack_ = mediaSource.player_.addTextTrack('captions', 'cc1');
  }

  if (segment.metadata &&
      segment.metadata.length &&
      !sourceBuffer.metadataTrack_) {
    sourceBuffer.metadataTrack_ = mediaSource.player_.addTextTrack('metadata', 'Timed Metadata');
    sourceBuffer.metadataTrack_.inBandMetadataTrackDispatchType = segment.metadata.dispatchType;
  }
};

addTextTrackData = function (sourceHandler, captionArray, metadataArray) {
  Cue = window.WebKitDataCue || window.VTTCue;
  if (captionArray) {
    captionArray.forEach(function (caption) {
      this.inbandTextTrack_.addCue(
        new Cue(
          caption.startTime + this.timestampOffset,
          caption.endTime + this.timestampOffset,
          caption.text
        ));
    }, sourceHandler);
  }

  if (metadataArray) {
    metadataArray.forEach(function(metadata) {
      var time = metadata.cueTime + this.timestampOffset;

      metadata.frames.forEach(function(frame) {
        var cue = new Cue(
            time,
            time,
            frame.value || frame.url || frame.data || '');

        cue.frame = frame;
        cue.value = frame;
        deprecateOldCue(cue);
        this.metadataTrack_.addCue(cue);
      }, this);
    }, sourceHandler);
  }
};

  // ------------
  // Media Source
  // ------------

  defaults = {
    // how to determine the MediaSource implementation to use. There
    // are three available modes:
    // - auto: use native MediaSources where available and Flash
    //   everywhere else
    // - html5: always use native MediaSources
    // - flash: always use the Flash MediaSource polyfill
    mode: 'auto'
  };

  videojs.MediaSource = function(options) {
    var settings = videojs.mergeOptions(defaults, options);

    // determine whether HTML MediaSources should be used
    if (settings.mode === 'html5' ||
        (settings.mode === 'auto' &&
         videojs.MediaSource.supportsNativeMediaSources())) {
      return new videojs.HtmlMediaSource();
    }

    // otherwise, emulate them through the SWF
    return new videojs.FlashMediaSource();
  };

  videojs.MediaSource.supportsNativeMediaSources = function() {
    return !!window.MediaSource;
  };

  // ----
  // HTML
  // ----

  videojs.HtmlMediaSource = videojs.extend(EventTarget, {
    constructor: function() {
      var self = this, property;

      this.mediaSource_ = new window.MediaSource();
      // delegate to the native MediaSource's methods by default
      for (property in this.mediaSource_) {
        if (!(property in videojs.HtmlMediaSource.prototype) &&
            typeof this.mediaSource_[property] === 'function') {
          this[property] = this.mediaSource_[property].bind(this.mediaSource_);
        }
      }

      // emulate `duration` and `seekable` until seeking can be
      // handled uniformly for live streams
      // see https://github.com/w3c/media-source/issues/5
      this.duration_ = NaN;
      Object.defineProperty(this, 'duration', {
        get: function() {
          return self.duration_;
        },
        set: function(duration) {
          var currentDuration;

          self.duration_ = duration;
          if (duration !== Infinity) {
            self.mediaSource_.duration = duration;
            return;
          }
        }
      });
      Object.defineProperty(this, 'seekable', {
        get: function() {
          if (this.duration_ === Infinity) {
            return videojs.createTimeRanges([[0, self.mediaSource_.duration]]);
          }
          return self.mediaSource_.seekable;
        }
      });

      Object.defineProperty(this, 'readyState', {
        get: function() {
          return self.mediaSource_.readyState;
        }
      });

      // the list of virtual and native SourceBuffers created by this
      // MediaSource
      this.sourceBuffers = [];

      // Re-emit MediaSource events on the polyfill
      [
        'sourceopen',
        'sourceclose',
        'sourceended'
      ].forEach(function(eventName) {
        this.mediaSource_.addEventListener(eventName, this.trigger.bind(this));
      }, this);

      // capture the associated player when the MediaSource is
      // successfully attached
      this.on('sourceopen', function(event) {
        var video = document.querySelector('[src="' + self.url_ + '"]');

        if (!video) {
          return;
        }

        self.player_ = videojs(video.parentNode);
      });

      // explicitly terminate any WebWorkers that were created
      // by SourceHandlers
      this.on('sourceclose', function(event) {
        this.sourceBuffers.forEach(function(sourceBuffer) {
          if (sourceBuffer.transmuxer_) {
            sourceBuffer.transmuxer_.terminate();
          }
        });

        this.sourceBuffers.length = 0;
      });
    },

    addSeekableRange_: function(start, end) {
      var error;

      if (this.duration !== Infinity) {
        error = new Error('MediaSource.addSeekableRange() can only be invoked ' +
                          'when the duration is Infinity');
        error.name = 'InvalidStateError';
        error.code = 11;
        throw error;
      }

      if (end > this.mediaSource_.duration ||
          isNaN(this.mediaSource_.duration)) {
        this.mediaSource_.duration = end;
      }
    },

    addSourceBuffer: function(type) {
      var
        buffer,
        codecs,
        avcCodec,
        mp4aCodec,
        avcRegEx = /avc1\.[\da-f]+/i,
        mp4aRegEx = /mp4a\.\d+.\d+/i;

      // create a virtual source buffer to transmux MPEG-2 transport
      // stream segments into fragmented MP4s
      if ((/^video\/mp2t/i).test(type)) {
        codecs = type.split(';').slice(1).join(';');
        codecs = translateLegacyCodecs(codecs);

        // Pull out each individual codec string if it exists
        avcCodec = (codecs.match(avcRegEx) || [])[0];
        mp4aCodec = (codecs.match(mp4aRegEx) || [])[0];

        // If a codec is unspecified, use the defaults
        if (!avcCodec || !avcCodec.length) {
          avcCodec = 'avc1.4d400d';
        }
        if (!mp4aCodec || !mp4aCodec.length) {
          mp4aCodec = 'mp4a.40.2';
        }

        buffer = new VirtualSourceBuffer(this, [avcCodec, mp4aCodec]);
        this.sourceBuffers.push(buffer);
        return buffer;
      }


      // delegate to the native implementation
      buffer = this.mediaSource_.addSourceBuffer(type);
      this.sourceBuffers.push(buffer);
      return buffer;
    }
  });

  // Replace the old apple-style `avc1.<dd>.<dd>` codec string with the standard
  // `avc1.<hhhhhh>`
  var translateLegacyCodecs = function(codecs) {
    return codecs.replace(/avc1\.(\d+)\.(\d+)/i, function(orig, profile, avcLevel) {
      var
      profileHex = ('00' + Number(profile).toString(16)).slice(-2),
      avcLevelHex = ('00' + Number(avcLevel).toString(16)).slice(-2);

      return 'avc1.' + profileHex + '00' + avcLevelHex;
    });
  };

  aggregateUpdateHandler = function(mediaSource, guardBufferName, type) {
    return function() {
      if (!mediaSource[guardBufferName] || !mediaSource[guardBufferName].updating) {
        return mediaSource.trigger(type);
      }
    };
  };

  VirtualSourceBuffer = videojs.extend(EventTarget, {
    constructor: function VirtualSourceBuffer(mediaSource, codecs) {
      var self = this;

      this.timestampOffset_ = 0;
      this.pendingBuffers_ = [];
      this.bufferUpdating_ = false;
      this.mediaSource_ = mediaSource;
      this.codecs_ = codecs;

      this.transmuxer_ = new Worker(URL.createObjectURL(new Blob(["var muxjs={},transmuxer,initOptions={};!function(a,b,c){var d=function(){this.init=function(){var a={};this.on=function(b,c){a[b]||(a[b]=[]),a[b].push(c)},this.off=function(b,c){var d;return a[b]?(d=a[b].indexOf(c),a[b].splice(d,1),d>-1):!1},this.trigger=function(b){var c,d,e,f;if(c=a[b])if(2===arguments.length)for(e=c.length,d=0;e>d;++d)c[d].call(this,arguments[1]);else{for(f=[],d=arguments.length,d=1;d<arguments.length;++d)f.push(arguments[d]);for(e=c.length,d=0;e>d;++d)c[d].apply(this,f)}},this.dispose=function(){a={}}}};d.prototype.pipe=function(a){return this.on(\"data\",function(b){a.push(b)}),this.on(\"done\",function(){a.flush()}),a},d.prototype.push=function(a){this.trigger(\"data\",a)},d.prototype.flush=function(){this.trigger(\"done\")},a.muxjs=a.muxjs||{},a.muxjs.utils=a.muxjs.utils||{},a.muxjs.utils.Stream=d}(this,this.muxjs),function(a,b){var c;c=function(a){var b=a.byteLength,c=0,d=0;this.length=function(){return 8*b},this.bitsAvailable=function(){return 8*b+d},this.loadWord=function(){var e=a.byteLength-b,f=new Uint8Array(4),g=Math.min(4,b);if(0===g)throw new Error(\"no bytes available\");f.set(a.subarray(e,e+g)),c=new DataView(f.buffer).getUint32(0),d=8*g,b-=g},this.skipBits=function(a){var e;d>a?(c<<=a,d-=a):(a-=d,e=Math.floor(a/8),a-=8*e,b-=e,this.loadWord(),c<<=a,d-=a)},this.readBits=function(a){var e=Math.min(d,a),f=c>>>32-e;return console.assert(32>a,\"Cannot read more than 32 bits at a time\"),d-=e,d>0?c<<=e:b>0&&this.loadWord(),e=a-e,e>0?f<<e|this.readBits(e):f},this.skipLeadingZeros=function(){var a;for(a=0;d>a;++a)if(0!==(c&2147483648>>>a))return c<<=a,d-=a,a;return this.loadWord(),a+this.skipLeadingZeros()},this.skipUnsignedExpGolomb=function(){this.skipBits(1+this.skipLeadingZeros())},this.skipExpGolomb=function(){this.skipBits(1+this.skipLeadingZeros())},this.readUnsignedExpGolomb=function(){var a=this.skipLeadingZeros();return this.readBits(a+1)-1},this.readExpGolomb=function(){var a=this.readUnsignedExpGolomb();return 1&a?1+a>>>1:-1*(a>>>1)},this.readBoolean=function(){return 1===this.readBits(1)},this.readUnsignedByte=function(){return this.readBits(8)},this.loadWord()},a.muxjs=b||{},b.utils=b.utils||{},b.utils.ExpGolomb=c}(this,this.muxjs),function(a,b,c){\"use strict\";var d,e,f,g,h,i,j,k,l,m,n,o,p,q,r,s,t,u,v,w,x,y,z,A,B,C,D,E,F,G,H,I,J,K,L,M,N,O,P;O=a.Uint8Array,P=a.DataView,function(){var a;A={avc1:[],avcC:[],btrt:[],dinf:[],dref:[],esds:[],ftyp:[],hdlr:[],mdat:[],mdhd:[],mdia:[],mfhd:[],minf:[],moof:[],moov:[],mp4a:[],mvex:[],mvhd:[],sdtp:[],smhd:[],stbl:[],stco:[],stsc:[],stsd:[],stsz:[],stts:[],styp:[],tfdt:[],tfhd:[],traf:[],trak:[],trun:[],trex:[],tkhd:[],vmhd:[]};for(a in A)A.hasOwnProperty(a)&&(A[a]=[a.charCodeAt(0),a.charCodeAt(1),a.charCodeAt(2),a.charCodeAt(3)]);B=new O([\"i\".charCodeAt(0),\"s\".charCodeAt(0),\"o\".charCodeAt(0),\"m\".charCodeAt(0)]),D=new O([\"a\".charCodeAt(0),\"v\".charCodeAt(0),\"c\".charCodeAt(0),\"1\".charCodeAt(0)]),C=new O([0,0,0,1]),E=new O([0,0,0,0,0,0,0,0,118,105,100,101,0,0,0,0,0,0,0,0,0,0,0,0,86,105,100,101,111,72,97,110,100,108,101,114,0]),F=new O([0,0,0,0,0,0,0,0,115,111,117,110,0,0,0,0,0,0,0,0,0,0,0,0,83,111,117,110,100,72,97,110,100,108,101,114,0]),G={video:E,audio:F},J=new O([0,0,0,0,0,0,0,1,0,0,0,12,117,114,108,32,0,0,0,1]),I=new O([0,0,0,0,0,0,0,0]),K=new O([0,0,0,0,0,0,0,0]),L=K,M=new O([0,0,0,0,0,0,0,0,0,0,0,0]),N=K,H=new O([0,0,0,1,0,0,0,0,0,0,0,0])}(),d=function(a){var b,c,d,e=[],f=0;for(b=1;b<arguments.length;b++)e.push(arguments[b]);for(b=e.length;b--;)f+=e[b].byteLength;for(c=new O(f+8),d=new P(c.buffer,c.byteOffset,c.byteLength),d.setUint32(0,c.byteLength),c.set(a,4),b=0,f=8;b<e.length;b++)c.set(e[b],f),f+=e[b].byteLength;return c},e=function(){return d(A.dinf,d(A.dref,J))},f=function(a){return d(A.esds,new O([0,0,0,0,3,25,0,0,0,4,17,64,21,0,6,0,0,0,218,192,0,0,218,192,5,2,a.audioobjecttype<<3|a.samplingfrequencyindex>>>1,a.samplingfrequencyindex<<7|a.channelcount<<3,6,1,2]))},g=function(){return d(A.ftyp,B,C,B,D)},s=function(a){return d(A.hdlr,G[a])},h=function(a){return d(A.mdat,a)},r=function(a){var b=new O([0,0,0,0,0,0,0,2,0,0,0,3,0,1,95,144,a.duration>>>24&255,a.duration>>>16&255,a.duration>>>8&255,255&a.duration,85,196,0,0]);return a.samplerate&&(b[12]=a.samplerate>>>24&255,b[13]=a.samplerate>>>16&255,b[14]=a.samplerate>>>8&255,b[15]=255&a.samplerate),d(A.mdhd,b)},q=function(a){return d(A.mdia,r(a),s(a.type),j(a))},i=function(a){return d(A.mfhd,new O([0,0,0,0,(4278190080&a)>>24,(16711680&a)>>16,(65280&a)>>8,255&a]))},j=function(a){return d(A.minf,\"video\"===a.type?d(A.vmhd,H):d(A.smhd,I),e(),u(a))},k=function(a,b){for(var c=[],e=b.length;e--;)c[e]=x(b[e]);return d.apply(null,[A.moof,i(a)].concat(c))},l=function(a){for(var b=a.length,c=[];b--;)c[b]=o(a[b]);return d.apply(null,[A.moov,n(4294967295)].concat(c).concat(m(a)))},m=function(a){for(var b=a.length,c=[];b--;)c[b]=y(a[b]);return d.apply(null,[A.mvex].concat(c))},n=function(a){var b=new O([0,0,0,0,0,0,0,1,0,0,0,2,0,1,95,144,(4278190080&a)>>24,(16711680&a)>>16,(65280&a)>>8,255&a,0,1,0,0,1,0,0,0,0,0,0,0,0,0,0,0,0,1,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,1,0,0,0,0,0,0,0,0,0,0,0,0,0,0,64,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,255,255,255,255]);return d(A.mvhd,b)},t=function(a){var b,c,e=a.samples||[],f=new O(4+e.length);for(c=0;c<e.length;c++)b=e[c].flags,f[c+4]=b.dependsOn<<4|b.isDependedOn<<2|b.hasRedundancy;return d(A.sdtp,f)},u=function(a){return d(A.stbl,v(a),d(A.stts,N),d(A.stsc,L),d(A.stsz,M),d(A.stco,K))},function(){var a,b;v=function(c){return d(A.stsd,new O([0,0,0,0,0,0,0,1]),\"video\"===c.type?a(c):b(c))},a=function(a){var b,c=a.sps||[],e=a.pps||[],f=[],g=[];for(b=0;b<c.length;b++)f.push((65280&c[b].byteLength)>>>8),f.push(255&c[b].byteLength),f=f.concat(Array.prototype.slice.call(c[b]));for(b=0;b<e.length;b++)g.push((65280&e[b].byteLength)>>>8),g.push(255&e[b].byteLength),g=g.concat(Array.prototype.slice.call(e[b]));return d(A.avc1,new O([0,0,0,0,0,0,0,1,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,(65280&a.width)>>8,255&a.width,(65280&a.height)>>8,255&a.height,0,72,0,0,0,72,0,0,0,0,0,0,0,1,19,118,105,100,101,111,106,115,45,99,111,110,116,114,105,98,45,104,108,115,0,0,0,0,0,0,0,0,0,0,0,0,0,24,17,17]),d(A.avcC,new O([1,a.profileIdc,a.profileCompatibility,a.levelIdc,255].concat([c.length]).concat(f).concat([e.length]).concat(g))),d(A.btrt,new O([0,28,156,128,0,45,198,192,0,45,198,192])))},b=function(a){return d(A.mp4a,new O([0,0,0,0,0,0,0,1,0,0,0,0,0,0,0,0,(65280&a.channelcount)>>8,255&a.channelcount,(65280&a.samplesize)>>8,255&a.samplesize,0,0,0,0,(65280&a.samplerate)>>8,255&a.samplerate,0,0]),f(a))}}(),w=function(){return d(A.styp,B,C,B)},p=function(a){var b=new O([0,0,0,7,0,0,0,0,0,0,0,0,(4278190080&a.id)>>24,(16711680&a.id)>>16,(65280&a.id)>>8,255&a.id,0,0,0,0,(4278190080&a.duration)>>24,(16711680&a.duration)>>16,(65280&a.duration)>>8,255&a.duration,0,0,0,0,0,0,0,0,0,0,0,0,1,0,0,0,0,1,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,1,0,0,0,0,0,0,0,0,0,0,0,0,0,0,64,0,0,0,(65280&a.width)>>8,255&a.width,0,0,(65280&a.height)>>8,255&a.height,0,0]);return d(A.tkhd,b)},x=function(a){var b,c,e,f,g;return b=d(A.tfhd,new O([0,0,0,58,(4278190080&a.id)>>24,(16711680&a.id)>>16,(65280&a.id)>>8,255&a.id,0,0,0,1,0,0,0,0,0,0,0,0,0,0,0,0])),c=d(A.tfdt,new O([0,0,0,0,a.baseMediaDecodeTime>>>24&255,a.baseMediaDecodeTime>>>16&255,a.baseMediaDecodeTime>>>8&255,255&a.baseMediaDecodeTime])),g=88,\"audio\"===a.type?(e=z(a,g),d(A.traf,b,c,e)):(f=t(a),e=z(a,f.length+g),d(A.traf,b,c,e,f))},o=function(a){return a.duration=a.duration||4294967295,d(A.trak,p(a),q(a))},y=function(a){var b=new O([0,0,0,0,(4278190080&a.id)>>24,(16711680&a.id)>>16,(65280&a.id)>>8,255&a.id,0,0,0,1,0,0,0,0,0,0,0,0,0,1,0,1]);return\"video\"!==a.type&&(b[b.length-1]=0),d(A.trex,b)},function(){var a,b,e;e=function(a,b){var d=0,e=0,f=0,g=0;return a.length&&(a[0].duration!==c&&(d=1),a[0].size!==c&&(e=2),a[0].flags!==c&&(f=4),a[0].compositionTimeOffset!==c&&(g=8)),[0,0,d|e|f|g,1,(4278190080&a.length)>>>24,(16711680&a.length)>>>16,(65280&a.length)>>>8,255&a.length,(4278190080&b)>>>24,(16711680&b)>>>16,(65280&b)>>>8,255&b]},b=function(a,b){var c,f,g,h;for(f=a.samples||[],b+=20+16*f.length,c=e(f,b),h=0;h<f.length;h++)g=f[h],c=c.concat([(4278190080&g.duration)>>>24,(16711680&g.duration)>>>16,(65280&g.duration)>>>8,255&g.duration,(4278190080&g.size)>>>24,(16711680&g.size)>>>16,(65280&g.size)>>>8,255&g.size,g.flags.isLeading<<2|g.flags.dependsOn,g.flags.isDependedOn<<6|g.flags.hasRedundancy<<4|g.flags.paddingValue<<1|g.flags.isNonSyncSample,61440&g.flags.degradationPriority,15&g.flags.degradationPriority,(4278190080&g.compositionTimeOffset)>>>24,(16711680&g.compositionTimeOffset)>>>16,(65280&g.compositionTimeOffset)>>>8,255&g.compositionTimeOffset]);return d(A.trun,new O(c))},a=function(a,b){var c,f,g,h;for(f=a.samples||[],b+=20+8*f.length,c=e(f,b),h=0;h<f.length;h++)g=f[h],c=c.concat([(4278190080&g.duration)>>>24,(16711680&g.duration)>>>16,(65280&g.duration)>>>8,255&g.duration,(4278190080&g.size)>>>24,(16711680&g.size)>>>16,(65280&g.size)>>>8,255&g.size]);return d(A.trun,new O(c))},z=function(c,d){return\"audio\"===c.type?a(c,d):b(c,d)}}(),b.mp4={ftyp:g,mdat:h,moof:k,moov:l,initSegment:function(a){var b,c=g(),d=l(a);return b=new O(c.byteLength+d.byteLength),b.set(c),b.set(d,c.byteLength),b}}}(this,this.muxjs),function(a,b){\"use strict\";var c,d=[96e3,88200,64e3,48e3,44100,32e3,24e3,22050,16e3,12e3,11025,8e3,7350];c=function(){var a,b;c.prototype.init.call(this),a=this,this.push=function(a){var c,e,f,g,h,i,j=0,k=0;if(\"audio\"===a.type)for(b?(g=b,b=new Uint8Array(g.byteLength+a.data.byteLength),b.set(g),b.set(a.data,g.byteLength)):b=a.data;j+5<b.length;)if(255===b[j]&&240===(246&b[j+1])){if(e=2*(1&~b[j+1]),c=(3&b[j+3])<<11|b[j+4]<<3|(224&b[j+5])>>5,h=1024*((3&b[j+6])+1),i=9e4*h/d[(60&b[j+2])>>>2],f=j+c,b.byteLength<f)return;if(this.trigger(\"data\",{pts:a.pts+k*i,dts:a.dts+k*i,sampleCount:h,audioobjecttype:(b[j+2]>>>6&3)+1,channelcount:(1&b[j+2])<<3|(192&b[j+3])>>>6,samplerate:d[(60&b[j+2])>>>2],samplingfrequencyindex:(60&b[j+2])>>>2,samplesize:16,data:b.subarray(j+7+e,f)}),b.byteLength===f)return void(b=void 0);k++,b=b.subarray(f)}else j++}},c.prototype=new b.utils.Stream,b.codecs=b.codecs||{},b.codecs.AacStream=c}(this,this.muxjs),function(a,b){\"use strict\";var c,d;d=function(){var a,b,c=0;d.prototype.init.call(this),this.push=function(d){var e;for(b?(e=new Uint8Array(b.byteLength+d.data.byteLength),e.set(b),e.set(d.data,b.byteLength),b=e):b=d.data;c<b.byteLength-3;c++)if(1===b[c+2]){a=c+5;break}for(;a<b.byteLength;)switch(b[a]){case 0:if(0!==b[a-1]){a+=2;break}if(0!==b[a-2]){a++;break}this.trigger(\"data\",b.subarray(c+3,a-2));do a++;while(1!==b[a]&&a<b.length);c=a-2,a+=3;break;case 1:if(0!==b[a-1]||0!==b[a-2]){a+=3;break}this.trigger(\"data\",b.subarray(c+3,a-2)),c=a-2,a+=3;break;default:a+=3}b=b.subarray(c),a-=c,c=0},this.flush=function(){b&&b.byteLength>3&&this.trigger(\"data\",b.subarray(c+3)),b=null,c=0,this.trigger(\"done\")}},d.prototype=new b.utils.Stream,c=function(){var a,e,f,g,h,i,j,k=new d;c.prototype.init.call(this),a=this,this.push=function(a){\"video\"===a.type&&(e=a.trackId,f=a.pts,g=a.dts,k.push(a))},k.on(\"data\",function(b){var c={trackId:e,pts:f,dts:g,data:b};switch(31&b[0]){case 5:c.nalUnitType=\"slice_layer_without_partitioning_rbsp_idr\";break;case 6:c.nalUnitType=\"sei_rbsp\",c.escapedRBSP=h(b.subarray(1));break;case 7:c.nalUnitType=\"seq_parameter_set_rbsp\",c.escapedRBSP=h(b.subarray(1)),c.config=i(c.escapedRBSP);break;case 8:c.nalUnitType=\"pic_parameter_set_rbsp\";break;case 9:c.nalUnitType=\"access_unit_delimiter_rbsp\"}a.trigger(\"data\",c)}),k.on(\"done\",function(){a.trigger(\"done\")}),this.flush=function(){k.flush()},j=function(a,b){var c,d,e=8,f=8;for(c=0;a>c;c++)0!==f&&(d=b.readExpGolomb(),f=(e+d+256)%256),e=0===f?e:f},h=function(a){for(var b,c,d=a.byteLength,e=[],f=1;d-2>f;)0===a[f]&&0===a[f+1]&&3===a[f+2]?(e.push(f+2),f+=2):f++;if(0===e.length)return a;b=d-e.length,c=new Uint8Array(b);var g=0;for(f=0;b>f;g++,f++)g===e[0]&&(g++,e.shift()),c[f]=a[g];return c},i=function(a){var c,d,e,f,g,h,i,k,l,m,n,o,p=0,q=0,r=0,s=0;if(c=new b.utils.ExpGolomb(a),d=c.readUnsignedByte(),f=c.readUnsignedByte(),e=c.readUnsignedByte(),c.skipUnsignedExpGolomb(),(100===d||110===d||122===d||244===d||44===d||83===d||86===d||118===d||128===d||138===d||139===d||134===d)&&(g=c.readUnsignedExpGolomb(),3===g&&c.skipBits(1),c.skipUnsignedExpGolomb(),c.skipUnsignedExpGolomb(),c.skipBits(1),c.readBoolean()))for(n=3!==g?8:12,o=0;n>o;o++)c.readBoolean()&&(6>o?j(16,c):j(64,c));if(c.skipUnsignedExpGolomb(),h=c.readUnsignedExpGolomb(),0===h)c.readUnsignedExpGolomb();else if(1===h)for(c.skipBits(1),c.skipExpGolomb(),c.skipExpGolomb(),i=c.readUnsignedExpGolomb(),o=0;i>o;o++)c.skipExpGolomb();return c.skipUnsignedExpGolomb(),c.skipBits(1),k=c.readUnsignedExpGolomb(),l=c.readUnsignedExpGolomb(),m=c.readBits(1),0===m&&c.skipBits(1),c.skipBits(1),c.readBoolean()&&(p=c.readUnsignedExpGolomb(),q=c.readUnsignedExpGolomb(),r=c.readUnsignedExpGolomb(),s=c.readUnsignedExpGolomb()),{profileIdc:d,levelIdc:e,profileCompatibility:f,width:16*(k+1)-2*p-2*q,height:(2-m)*(l+1)*16-2*r-2*s}}},c.prototype=new b.utils.Stream,b.codecs=b.codecs||{},b.codecs.H264Stream=c,b.codecs.NalByteStream=d}(this,this.muxjs),function(a,b,c){\"use strict\";var d,e,f,g,h,i,j,k;g=188,k=71,h=27,i=15,j=21,d=function(){var a=new Uint8Array(g),b=0;d.prototype.init.call(this),this.push=function(c){var d,e=0,f=g;for(b?(d=new Uint8Array(c.byteLength+b),d.set(a.subarray(0,b)),d.set(c,b),b=0):d=c;f<d.byteLength;)d[e]!==k||d[f]!==k?(e++,f++):(this.trigger(\"data\",d.subarray(e,f)),e+=g,f+=g);e<d.byteLength&&(a.set(d.subarray(e),0),b=d.byteLength-e)},this.flush=function(){b===g&&a[0]===k&&(this.trigger(\"data\",a),b=0),this.trigger(\"done\")}},d.prototype=new b.utils.Stream,e=function(){var a,b,d,f;e.prototype.init.call(this),f=this,this.packetsWaitingForPmt=[],this.programMapTable=c,a=function(a,c){var e=0;c.payloadUnitStartIndicator&&(e+=a[e]+1),\"pat\"===c.type?b(a.subarray(e),c):d(a.subarray(e),c)},b=function(a,b){b.section_number=a[7],b.last_section_number=a[8],f.pmtPid=(31&a[10])<<8|a[11],b.pmtPid=f.pmtPid},d=function(a,b){var c,d,e,g;if(1&a[5]){for(f.programMapTable={},c=(15&a[1])<<8|a[2],d=3+c-4,e=(15&a[10])<<8|a[11],g=12+e;d>g;)f.programMapTable[(31&a[g+1])<<8|a[g+2]]=a[g],g+=((15&a[g+3])<<8|a[g+4])+5;for(b.programMapTable=f.programMapTable;f.packetsWaitingForPmt.length;)f.processPes_.apply(f,f.packetsWaitingForPmt.shift())}},this.push=function(b){var d={},e=4;d.payloadUnitStartIndicator=!!(64&b[1]),d.pid=31&b[1],d.pid<<=8,d.pid|=b[2],(48&b[3])>>>4>1&&(e+=b[e]+1),0===d.pid?(d.type=\"pat\",a(b.subarray(e),d),this.trigger(\"data\",d)):d.pid===this.pmtPid?(d.type=\"pmt\",a(b.subarray(e),d),this.trigger(\"data\",d)):this.programMapTable===c?this.packetsWaitingForPmt.push([b,e,d]):this.processPes_(b,e,d)},this.processPes_=function(a,b,c){c.streamType=this.programMapTable[c.pid],c.type=\"pes\",c.data=a.subarray(b),this.trigger(\"data\",c)}},e.prototype=new b.utils.Stream,e.STREAM_TYPES={h264:27,adts:15},f=function(){var a,b={data:[],size:0},c={data:[],size:0},d={data:[],size:0},e=function(a,b){var c;b.dataAlignmentIndicator=0!==(4&a[6]),c=a[7],192&c&&(b.pts=(14&a[9])<<27|(255&a[10])<<20|(254&a[11])<<12|(255&a[12])<<5|(254&a[13])>>>3,b.pts*=4,b.pts+=(6&a[13])>>>1,b.dts=b.pts,64&c&&(b.dts=(14&a[14])<<27|(255&a[15])<<20|(254&a[16])<<12|(255&a[17])<<5|(254&a[18])>>>3,b.dts*=4,b.dts+=(6&a[18])>>>1)),b.data=a.subarray(9+a[8])},g=function(b,c){var d,f=new Uint8Array(b.size),g={type:c},h=0;if(b.data.length){for(g.trackId=b.data[0].pid;b.data.length;)d=b.data.shift(),f.set(d.data,h),h+=d.data.byteLength;e(f,g),b.size=0,a.trigger(\"data\",g)}};f.prototype.init.call(this),a=this,this.push=function(e){({pat:function(){},pes:function(){var a,f;switch(e.streamType){case h:a=b,f=\"video\";break;case i:a=c,f=\"audio\";break;case j:a=d,f=\"timed-metadata\";break;default:return}e.payloadUnitStartIndicator&&g(a,f),a.data.push(e),a.size+=e.data.byteLength},pmt:function(){var b,c,d={type:\"metadata\",tracks:[]},f=e.programMapTable;for(b in f)f.hasOwnProperty(b)&&(c={timelineStartInfo:{baseMediaDecodeTime:0}},c.id=+b,f[b]===h?(c.codec=\"avc\",c.type=\"video\"):f[b]===i&&(c.codec=\"adts\",c.type=\"audio\"),d.tracks.push(c));a.trigger(\"data\",d)}})[e.type]()},this.flush=function(){g(b,\"video\"),g(c,\"audio\"),g(d,\"timed-metadata\"),this.trigger(\"done\")}},f.prototype=new b.utils.Stream,b.mp2t=b.mp2t||{},b.mp2t.PAT_PID=0,b.mp2t.MP2T_PACKET_LENGTH=g,b.mp2t.H264_STREAM_TYPE=h,b.mp2t.ADTS_STREAM_TYPE=i,b.mp2t.METADATA_STREAM_TYPE=j,b.mp2t.TransportPacketStream=d,b.mp2t.TransportParseStream=e,b.mp2t.ElementaryStream=f}(this,this.muxjs),function(a,b,c){\"use strict\";var d=4,e=128,f=function(a){for(var b=0,c={payloadType:-1,payloadSize:0},f=0,g=0;b<a.byteLength&&a[b]!==e;){for(;255===a[b];)f+=255,b++;for(f+=a[b++];255===a[b];)g+=255,b++;if(g+=a[b++],!c.payload&&f===d){c.payloadType=f,c.payloadSize=g,c.payload=a.subarray(b,b+g);break}b+=g,f=0,g=0}return c},g=function(a){return 181!==a.payload[0]?null:49!==(a.payload[1]<<8|a.payload[2])?null:\"GA94\"!==String.fromCharCode(a.payload[3],a.payload[4],a.payload[5],a.payload[6])?null:3!==a.payload[7]?null:a.payload.subarray(8,a.payload.length-1)},h=function(a,b){var c,d,e,f,g=[];if(!(64&b[0]))return g;for(d=31&b[0],c=0;d>c;c++)e=3*c,f={type:3&b[e+2],pts:a},4&b[e+2]&&(f.ccData=b[e+3]<<8|b[e+4],g.push(f));return g},i=function(){i.prototype.init.call(this),this.captionPackets_=[],this.field1_=new w,this.field1_.on(\"data\",this.trigger.bind(this,\"data\")),this.field1_.on(\"done\",this.trigger.bind(this,\"done\"))};i.prototype=new b.utils.Stream,i.prototype.push=function(a){var b,c;\"sei_rbsp\"===a.nalUnitType&&(b=f(a.escapedRBSP),b.payloadType===d&&(c=g(b),c&&(this.captionPackets_=this.captionPackets_.concat(h(a.pts,c)))))},i.prototype.flush=function(){return this.captionPackets_.length?(this.captionPackets_.sort(function(a,b){return a.pts-b.pts}),this.captionPackets_.forEach(this.field1_.push,this.field1_),this.captionPackets_.length=0,void this.field1_.flush()):void this.field1_.flush()};var j={42:225,92:233,94:237,95:243,96:250,123:231,124:247,125:209,126:241,127:9608},k=0,l=5152,m=5167,n=5157,o=5158,p=5159,q=5165,r=5153,s=5164,t=5166,u=14,v=function(){for(var a=[],b=u+1;b--;)a.push(\"\");return a},w=function(){w.prototype.init.call(this),this.mode_=\"popOn\",this.topRow_=0,this.startPts_=0,this.displayed_=v(),this.nonDisplayed_=v(),this.lastControlCode_=null,this.push=function(a){var b,c,d,e;if(b=32639&a.ccData,b===this.lastControlCode_)return void(this.lastControlCode_=null);switch(4096===(61440&b)?this.lastControlCode_=b:this.lastControlCode_=null,b){case k:break;case l:this.mode_=\"popOn\";break;case m:this.flushDisplayed(a.pts),c=this.displayed_,this.displayed_=this.nonDisplayed_,this.nonDisplayed_=c,this.startPts_=a.pts;break;case n:this.topRow_=u-1,this.mode_=\"rollUp\";break;case o:this.topRow_=u-2,this.mode_=\"rollUp\";break;case p:this.topRow_=u-3,this.mode_=\"rollUp\";break;case q:this.flushDisplayed(a.pts),this.shiftRowsUp_(),this.startPts_=a.pts;break;case r:\"popOn\"===this.mode_?this.nonDisplayed_[u]=this.nonDisplayed_[u].slice(0,-1):this.displayed_[u]=this.displayed_[u].slice(0,-1);break;case s:this.flushDisplayed(a.pts),this.displayed_=v();break;case t:this.nonDisplayed_=v();break;default:if(d=b>>>8,e=255&b,d>=16&&23>=d&&e>=64&&127>=e&&(16!==d||96>e)&&(d=e=32),16===(240&d))return;this[this.mode_](a.pts,d,e)}}};w.prototype=new b.utils.Stream,w.prototype.flushDisplayed=function(a){var b,c;for(c=0;c<this.displayed_.length;c++)b=this.displayed_[c],b.length&&this.trigger(\"data\",{startPts:this.startPts_,endPts:a,text:b})},w.prototype.popOn=function(a,b,c){var d=this.nonDisplayed_[u];b=j[b]||b,d+=String.fromCharCode(b),c=j[c]||c,d+=String.fromCharCode(c),this.nonDisplayed_[u]=d},w.prototype.rollUp=function(a,b,c){var d=this.displayed_[u];\"\"===d&&(this.flushDisplayed(a),this.startPts_=a),b=j[b]||b,d+=String.fromCharCode(b),c=j[c]||c,d+=String.fromCharCode(c),this.displayed_[u]=d},w.prototype.shiftRowsUp_=function(){var a;for(a=0;a<this.topRow_;a++)this.displayed_[a]=\"\";for(a=this.topRow_;u>a;a++)this.displayed_[a]=this.displayed_[a+1];this.displayed_[u]=\"\"},b.mp2t=b.mp2t||{},b.mp2t.CaptionStream=i,b.mp2t.Cea608Stream=w}(this,this.muxjs),function(a,b,c){\"use strict\";var d,e=function(a,b,c){var d,e=\"\";for(d=b;c>d;d++)e+=\"%\"+(\"00\"+a[d].toString(16)).slice(-2);return e},f=function(b,c,d){return a.decodeURIComponent(e(b,c,d))},g=function(b,c,d){return a.unescape(e(b,c,d))},h=function(a){return a[0]<<21|a[1]<<14|a[2]<<7|a[3]},i={TXXX:function(a){var b;if(3===a.data[0]){for(b=1;b<a.data.length;b++)if(0===a.data[b]){a.description=f(a.data,1,b),a.value=f(a.data,b+1,a.data.length-1);break}a.data=a.value}},WXXX:function(a){var b;if(3===a.data[0])for(b=1;b<a.data.length;b++)if(0===a.data[b]){a.description=f(a.data,1,b),a.url=f(a.data,b+1,a.data.length);break}},PRIV:function(a){var b;for(b=0;b<a.data.length;b++)if(0===a.data[b]){a.owner=g(a.data,0,b);break}a.privateData=a.data.subarray(b+1),a.data=a.privateData}};d=function(a){var c,e={debug:!(!a||!a.debug),descriptor:a&&a.descriptor},f=0,g=[],j=0;if(d.prototype.init.call(this),this.dispatchType=b.mp2t.METADATA_STREAM_TYPE.toString(16),e.descriptor)for(c=0;c<e.descriptor.length;c++)this.dispatchType+=(\"00\"+e.descriptor[c].toString(16)).slice(-2);this.push=function(a){var b,c,d,k,l;if(\"timed-metadata\"===a.type){if(a.dataAlignmentIndicator&&(j=0,g.length=0),0===g.length&&(a.data.length<10||a.data[0]!==\"I\".charCodeAt(0)||a.data[1]!==\"D\".charCodeAt(0)||a.data[2]!==\"3\".charCodeAt(0)))return void(e.debug&&console.log(\"Skipping unrecognized metadata packet\"));if(g.push(a),j+=a.data.byteLength,1===g.length&&(f=h(a.data.subarray(6,10)),f+=10),!(f>j)){for(b={data:new Uint8Array(f),frames:[],pts:g[0].pts,dts:g[0].dts},l=0;f>l;)b.data.set(g[0].data.subarray(0,f-l),l),l+=g[0].data.byteLength,j-=g[0].data.byteLength,g.shift();c=10,64&b.data[5]&&(c+=4,c+=h(b.data.subarray(10,14)),f-=h(b.data.subarray(16,20)));do{if(d=h(b.data.subarray(c+4,c+8)),1>d)return console.log(\"Malformed ID3 frame encountered. Skipping metadata parsing.\");k={id:String.fromCharCode(b.data[c],b.data[c+1],b.data[c+2],b.data[c+3]),data:b.data.subarray(c+10,c+d+10)},k.key=k.id,i[k.id]&&i[k.id](k),b.frames.push(k),c+=10,c+=d}while(f>c);this.trigger(\"data\",b)}}}},d.prototype=new b.utils.Stream,b.mp2t=b.mp2t||{},b.mp2t.MetadataStream=d}(this,this.muxjs),function(a,b,c){\"use strict\";var d,e,f,g,h,i,j,k=b.mp4;e=function(a){var b=[],c=0,d=0,f=0;e.prototype.init.call(this),this.push=function(d){h(a,d),a&&(a.audioobjecttype=d.audioobjecttype,a.channelcount=d.channelcount,a.samplerate=d.samplerate,a.samplingfrequencyindex=d.samplingfrequencyindex,a.samplesize=d.samplesize),b.push(d),c+=d.data.byteLength},this.setEarliestDts=function(b){f=b-a.timelineStartInfo.baseMediaDecodeTime},this.flush=function(){var e,g,h,l,m,n,o;if(0===c)return void this.trigger(\"done\");for(a.minSegmentDts<f&&(a.minSegmentDts=1/0,b=b.filter(function(b){return b.dts>=f?(a.minSegmentDts=Math.min(a.minSegmentDts,b.dts),a.minSegmentPts=a.minSegmentDts,!0):(c-=b.data.byteLength,!1)})),h=new Uint8Array(c),a.samples=[],m=0;b.length;)g=b[0],l={size:g.data.byteLength,duration:1024},a.samples.push(l),h.set(g.data,m),m+=g.data.byteLength,b.shift();c=0,n=k.mdat(h),j(a),o=k.moof(d,[a]),e=new Uint8Array(o.byteLength+n.byteLength),d++,e.set(o),e.set(n,o.byteLength),i(a),this.trigger(\"data\",{track:a,boxes:e}),this.trigger(\"done\")}},e.prototype=new b.utils.Stream,d=function(a){var b,e,f=0,g=[],l=0;d.prototype.init.call(this),delete a.minPTS,this.push=function(c){h(a,c),\"seq_parameter_set_rbsp\"!==c.nalUnitType||b||(b=c.config,a.width=b.width,a.height=b.height,a.sps=[c.data],a.profileIdc=b.profileIdc,a.levelIdc=b.levelIdc,a.profileCompatibility=b.profileCompatibility),\"pic_parameter_set_rbsp\"!==c.nalUnitType||e||(e=c.data,a.pps=[c.data]),g.push(c),l+=c.data.byteLength},this.flush=function(){for(var d,h,m,n,o,p,q,r,s,t;g.length&&\"access_unit_delimiter_rbsp\"!==g[0].nalUnitType;)g.shift();if(0===l)return void this.trigger(\"done\");for(q=new Uint8Array(l+4*g.length),r=new DataView(q.buffer),a.samples=[],s={size:0,flags:{isLeading:0,dependsOn:1,isDependedOn:0,hasRedundancy:0,degradationPriority:0}},p=0;g.length;)h=g[0],\"access_unit_delimiter_rbsp\"===h.nalUnitType&&(d&&(s.duration=h.dts-d.dts,a.samples.push(s)),s={size:0,flags:{isLeading:0,dependsOn:1,isDependedOn:0,hasRedundancy:0,degradationPriority:0},dataOffset:p,compositionTimeOffset:h.pts-h.dts},d=h),\"slice_layer_without_partitioning_rbsp_idr\"===h.nalUnitType&&(s.flags.dependsOn=2),s.size+=4,s.size+=h.data.byteLength,r.setUint32(p,h.data.byteLength),p+=4,q.set(h.data,p),p+=h.data.byteLength,g.shift();for(a.samples.length&&(s.duration=a.samples[a.samples.length-1].duration),a.samples.push(s),t=0;a.samples.length;){if(s=a.samples[0],2===s.flags.dependsOn){q=q.subarray(s.dataOffset),s.duration+=t;break}t+=s.duration,a.samples.shift()}l=0,n=k.mdat(q),j(a),this.trigger(\"timelineStartInfo\",a.timelineStartInfo),m=k.moof(f,[a]),o=new Uint8Array(m.byteLength+n.byteLength),f++,o.set(m),o.set(n,m.byteLength),i(a),this.trigger(\"data\",{track:a,boxes:o}),b=c,e=c,this.trigger(\"done\")}},d.prototype=new b.utils.Stream,h=function(a,b){\"number\"==typeof b.pts&&(a.timelineStartInfo.pts===c&&(a.timelineStartInfo.pts=b.pts),a.minSegmentPts===c?a.minSegmentPts=b.pts:a.minSegmentPts=Math.min(a.minSegmentPts,b.pts),a.maxSegmentPts===c?a.maxSegmentPts=b.pts:a.maxSegmentPts=Math.max(a.maxSegmentPts,b.pts)),\"number\"==typeof b.dts&&(a.timelineStartInfo.dts===c&&(a.timelineStartInfo.dts=b.dts),a.minSegmentDts===c?a.minSegmentDts=b.dts:a.minSegmentDts=Math.min(a.minSegmentDts,b.dts),a.maxSegmentDts===c?a.maxSegmentDts=b.dts:a.maxSegmentDts=Math.max(a.maxSegmentDts,b.dts))},i=function(a){delete a.minSegmentDts,delete a.maxSegmentDts,delete a.minSegmentPts,delete a.maxSegmentPts},j=function(a){var b,c=9e4,d=a.minSegmentDts-a.timelineStartInfo.dts,e=a.minSegmentPts-a.minSegmentDts;a.baseMediaDecodeTime=a.timelineStartInfo.baseMediaDecodeTime,a.baseMediaDecodeTime+=d,a.baseMediaDecodeTime-=e,a.baseMediaDecodeTime=Math.max(0,a.baseMediaDecodeTime),\"audio\"===a.type&&(b=a.samplerate/c,a.baseMediaDecodeTime*=b,a.baseMediaDecodeTime=Math.floor(a.baseMediaDecodeTime))},g=function(a){this.numberOfTracks=0,this.metadataStream=a.metadataStream,\"undefined\"!=typeof a.remux?this.remuxTracks=!!a.remux:this.remuxTracks=!0,this.pendingTracks=[],this.videoTrack=null,this.pendingBoxes=[],this.pendingCaptions=[],this.pendingMetadata=[],this.pendingBytes=0,this.emittedTracks=0,g.prototype.init.call(this),this.push=function(a){return a.text?this.pendingCaptions.push(a):a.frames?this.pendingMetadata.push(a):(this.pendingTracks.push(a.track),this.pendingBoxes.push(a.boxes),this.pendingBytes+=a.boxes.byteLength,\"video\"===a.track.type&&(this.videoTrack=a.track),void(\"audio\"===a.track.type&&(this.audioTrack=a.track)))}},g.prototype=new b.utils.Stream,g.prototype.flush=function(){var a,c,d,e,f=0,g={captions:[],metadata:[]},h=0;if(!(0===this.pendingTracks.length||this.remuxTracks&&this.pendingTracks.length<this.numberOfTracks)){for(this.videoTrack?h=this.videoTrack.timelineStartInfo.pts:this.audioTrack&&(h=this.audioTrack.timelineStartInfo.pts),1===this.pendingTracks.length?g.type=this.pendingTracks[0].type:g.type=\"combined\",this.emittedTracks+=this.pendingTracks.length,d=b.mp4.initSegment(this.pendingTracks),g.data=new Uint8Array(d.byteLength+this.pendingBytes),g.data.set(d),f+=d.byteLength,e=0;e<this.pendingBoxes.length;e++)g.data.set(this.pendingBoxes[e],f),f+=this.pendingBoxes[e].byteLength;for(e=0;e<this.pendingCaptions.length;e++)a=this.pendingCaptions[e],a.startTime=a.startPts-h,a.startTime/=9e4,a.endTime=a.endPts-h,a.endTime/=9e4,g.captions.push(a);for(e=0;e<this.pendingMetadata.length;e++)c=this.pendingMetadata[e],c.cueTime=c.pts-h,c.cueTime/=9e4,g.metadata.push(c);g.metadata.dispatchType=this.metadataStream.dispatchType,this.pendingTracks.length=0,this.videoTrack=null,this.pendingBoxes.length=0,this.pendingCaptions.length=0,this.pendingBytes=0,this.pendingMetadata.length=0,this.trigger(\"data\",g),this.emittedTracks>=this.numberOfTracks&&(this.trigger(\"done\"),this.emittedTracks=0)}},f=function(a){var h,j,k,l,m,n,o,p,q,r,s,t=this;f.prototype.init.call(this),a=a||{},this.baseMediaDecodeTime=a.baseMediaDecodeTime||0,this.metadataStream=new b.mp2t.MetadataStream,a.metadataStream=this.metadataStream,k=new b.mp2t.TransportPacketStream,l=new b.mp2t.TransportParseStream,m=new b.mp2t.ElementaryStream,n=new b.codecs.AacStream,o=new b.codecs.H264Stream,s=new g(a),k.pipe(l).pipe(m),m.pipe(o),m.pipe(n),m.pipe(this.metadataStream).pipe(s),b.mp2t.CaptionStream&&(r=new b.mp2t.CaptionStream,o.pipe(r).pipe(s)),m.on(\"data\",function(a){var b;if(\"metadata\"===a.type){for(b=a.tracks.length;b--;)h||\"video\"!==a.tracks[b].type?j||\"audio\"!==a.tracks[b].type||(j=a.tracks[b],j.timelineStartInfo.baseMediaDecodeTime=t.baseMediaDecodeTime):(h=a.tracks[b],h.timelineStartInfo.baseMediaDecodeTime=t.baseMediaDecodeTime);h&&!p&&(s.numberOfTracks++,p=new d(h),p.on(\"timelineStartInfo\",function(a){j&&(j.timelineStartInfo=a,q.setEarliestDts(a.dts))}),o.pipe(p).pipe(s)),j&&!q&&(s.numberOfTracks++,q=new e(j),n.pipe(q).pipe(s))}}),this.setBaseMediaDecodeTime=function(a){this.baseMediaDecodeTime=a,j&&(j.timelineStartInfo.dts=c,j.timelineStartInfo.pts=c,i(j),j.timelineStartInfo.baseMediaDecodeTime=a),h&&(h.timelineStartInfo.dts=c,h.timelineStartInfo.pts=c,i(h),h.timelineStartInfo.baseMediaDecodeTime=a)},this.push=function(a){k.push(a)},this.flush=function(){k.flush()},s.on(\"data\",function(a){t.trigger(\"data\",a)}),s.on(\"done\",function(){t.trigger(\"done\")})},f.prototype=new b.utils.Stream,b.mp4=b.mp4||{},b.mp4.VideoSegmentStream=d,b.mp4.AudioSegmentStream=e,b.mp4.Transmuxer=f}(this,this.muxjs);var wireTransmuxerEvents=function(a){a.on(\"data\",function(a){var b=a.data;a.data=b.buffer,postMessage({action:\"data\",segment:a,byteOffset:b.byteOffset,byteLength:b.byteLength},[a.data])}),a.captionStream&&a.captionStream.on(\"data\",function(a){postMessage({action:\"caption\",data:a})}),a.on(\"done\",function(a){postMessage({action:\"done\"})})},messageHandlers={init:function(a){initOptions=a&&a.options||{},this.defaultInit()},defaultInit:function(){transmuxer&&transmuxer.dispose(),transmuxer=new muxjs.mp4.Transmuxer(initOptions),wireTransmuxerEvents(transmuxer)},push:function(a){var b=new Uint8Array(a.data,a.byteOffset,a.byteLength);transmuxer.push(b)},reset:function(){this.defaultInit()},setTimestampOffset:function(a){var b=a.timestampOffset||0;transmuxer.setBaseMediaDecodeTime(Math.round(9e4*b))},flush:function(a){transmuxer.flush()}};onmessage=function(a){transmuxer||\"init\"===a.data.action||messageHandlers.defaultInit(),a.data&&a.data.action&&messageHandlers[a.data.action]&&messageHandlers[a.data.action](a.data)};"], {type: "application/javascript"})));
      this.transmuxer_.postMessage({action:'init', options: {remux: false}});

      this.transmuxer_.onmessage = function (event) {
        if (event.data.action === 'data') {
          return self.data_(event);
        }

        if (event.data.action === 'done') {
          return self.done_(event);
        }
      };

      // this timestampOffset is a property with the side-effect of resetting
      // baseMediaDecodeTime in the transmuxer on the setter
      Object.defineProperty(this, 'timestampOffset', {
        get: function() {
          return this.timestampOffset_;
        },
        set: function(val) {
          if (typeof val === 'number' && val >= 0) {
            this.timestampOffset_ = val;

            // We have to tell the transmuxer to set the baseMediaDecodeTime to
            // the desired timestampOffset for the next segment
            this.transmuxer_.postMessage({
              action: 'setTimestampOffset',
              timestampOffset: val
            });
          }
        }
      });
      // setting the append window affects both source buffers
      Object.defineProperty(this, 'appendWindowStart', {
        get: function() {
          return (this.videoBuffer_ || this.audioBuffer_).appendWindowStart;
        },
        set: function(start) {
          if (this.videoBuffer_) {
            this.videoBuffer_.appendWindowStart = start;
          }
          if (this.audioBuffer_) {
            this.audioBuffer_.appendWindowStart = start;
          }
        }
      });
      // this buffer is "updating" if either of its native buffers are
      Object.defineProperty(this, 'updating', {
        get: function() {
          return this.bufferUpdating_ ||
            (this.audioBuffer_ && this.audioBuffer_.updating) ||
            (this.videoBuffer_ && this.videoBuffer_.updating);
        }
      });
      // the buffered property is the intersection of the buffered
      // ranges of the native source buffers
      Object.defineProperty(this, 'buffered', {
        get: function() {
          var
            start = null,
            end = null,
            arity = 0,
            extents = [],
            ranges = [];

          // Handle the case where there is no buffer data
          if ((!this.videoBuffer_ || this.videoBuffer_.buffered.length === 0) &&
              (!this.audioBuffer_ || this.audioBuffer_.buffered.length === 0)) {
            return videojs.createTimeRange();
          }

          // Handle the case where we only have one buffer
          if (!this.videoBuffer_) {
            return this.audioBuffer_.buffered;
          } else if (!this.audioBuffer_) {
            return this.videoBuffer_.buffered;
          }

          // Handle the case where we have both buffers and create an
          // intersection of the two
          var videoIndex = 0, audioIndex = 0;
          var videoBuffered = this.videoBuffer_.buffered;
          var audioBuffered = this.audioBuffer_.buffered;
          var count = videoBuffered.length;

          // A) Gather up all start and end times
          while (count--) {
            extents.push({time: videoBuffered.start(count), type: 'start'});
            extents.push({time: videoBuffered.end(count), type: 'end'});
          }
          count = audioBuffered.length;
          while (count--) {
            extents.push({time: audioBuffered.start(count), type: 'start'});
            extents.push({time: audioBuffered.end(count), type: 'end'});
          }
          // B) Sort them by time
          extents.sort(function(a, b){return a.time - b.time;});

          // C) Go along one by one incrementing arity for start and decrementing
          //    arity for ends
          for(count = 0; count < extents.length; count++) {
            if (extents[count].type === 'start') {
              arity++;

              // D) If arity is ever incremented to 2 we are entering an
              //    overlapping range
              if (arity === 2) {
                start = extents[count].time;
              }
            } else if (extents[count].type === 'end') {
              arity--;

              // E) If arity is ever decremented to 1 we leaving an
              //    overlapping range
              if (arity === 1) {
                end = extents[count].time;
              }
            }

            // F) Record overlapping ranges
            if (start !== null && end !== null) {
              ranges.push([start, end]);
              start = null;
              end = null;
            }
          }

          return videojs.createTimeRanges(ranges);
        }
      });
    },

    // Transmuxer message handlers

    data_: function(event) {
      var
        segment = event.data.segment,
        nativeMediaSource = this.mediaSource_.mediaSource_;

      // Cast ArrayBuffer to TypedArray
      segment.data = new Uint8Array(segment.data, event.data.byteOffset, event.data.byteLength);

      // If any sourceBuffers have not been created, do so now
      if (segment.type === 'video') {
        if (!this.videoBuffer_) {
          this.videoBuffer_ = nativeMediaSource.addSourceBuffer('video/mp4;codecs="' + this.codecs_[0] + '"');
          // aggregate buffer events
          this.videoBuffer_.addEventListener('updatestart',
                                             aggregateUpdateHandler(this, 'audioBuffer_', 'updatestart'));
          this.videoBuffer_.addEventListener('update',
                                             aggregateUpdateHandler(this, 'audioBuffer_', 'update'));
          this.videoBuffer_.addEventListener('updateend',
                                             aggregateUpdateHandler(this, 'audioBuffer_', 'updateend'));
        }
      } else if (segment.type === 'audio') {
        if (!this.audioBuffer_) {
          this.audioBuffer_ = nativeMediaSource.addSourceBuffer('audio/mp4;codecs="' + this.codecs_[1] + '"');
          // aggregate buffer events
          this.audioBuffer_.addEventListener('updatestart',
                                             aggregateUpdateHandler(this, 'videoBuffer_', 'updatestart'));
          this.audioBuffer_.addEventListener('update',
                                             aggregateUpdateHandler(this, 'videoBuffer_', 'update'));
          this.audioBuffer_.addEventListener('updateend',
                                             aggregateUpdateHandler(this, 'videoBuffer_', 'updateend'));
        }
      } else if (segment.type === 'combined') {
        if (!this.videoBuffer_) {
          this.videoBuffer_ = nativeMediaSource.addSourceBuffer('video/mp4;codecs="' + this.codecs_.join(',') + '"');
          // aggregate buffer events
          this.videoBuffer_.addEventListener('updatestart',
                                             aggregateUpdateHandler(this, 'videoBuffer_', 'updatestart'));
          this.videoBuffer_.addEventListener('update',
                                             aggregateUpdateHandler(this, 'videoBuffer_', 'update'));
          this.videoBuffer_.addEventListener('updateend',
                                             aggregateUpdateHandler(this, 'videoBuffer_', 'updateend'));
        }
      }
      createTextTracksIfNecessary(this, this.mediaSource_, segment);

      // Add the segments to the pendingBuffers array
      this.pendingBuffers_.push(segment);
      return;
    },
    done_: function() {
      // All buffers should have been flushed from the muxer
      // start processing anything we have received
      this.processPendingSegments_();
      return;
    },

    // SourceBuffer Implementation

    appendBuffer: function(segment) {
      // Start the internal "updating" state
      this.bufferUpdating_ = true;

      this.transmuxer_.postMessage({
        action: 'push',
        // Send the typed-array of data as an ArrayBuffer so that
        // it can be sent as a "Transferable" and avoid the costly
        // memory copy
        data: segment.buffer,

        // To recreate the original typed-array, we need information
        // about what portion of the ArrayBuffer it was a view into
        byteOffset: segment.byteOffset,
        byteLength: segment.byteLength
      },
      [segment.buffer]);
      this.transmuxer_.postMessage({action: 'flush'});
    },
    remove: function(start, end) {
      if (this.videoBuffer_) {
        this.videoBuffer_.remove(start, end);
      }
      if (this.audioBuffer_) {
        this.audioBuffer_.remove(start, end);
      }

      // Remove Metadata Cues (id3)
      removeCuesFromTrack(start, end, this.metadataTrack_);

      // Remove Any Captions
      removeCuesFromTrack(start, end, this.inbandTextTrack_);
    },

    /**
     * Process any segments that the muxer has output
     * Concatenate segments together based on type and append them into
     * their respective sourceBuffers
     */
    processPendingSegments_: function() {
      var sortedSegments = {
          video: {
            segments: [],
            bytes: 0
          },
          audio: {
            segments: [],
            bytes: 0
          },
          captions: [],
          metadata: []
        };

      // Sort segments into separate video/audio arrays and
      // keep track of their total byte lengths
      sortedSegments = this.pendingBuffers_.reduce(function (segmentObj, segment) {
        var
          type = segment.type,
          data = segment.data;

        // A "combined" segment type (unified video/audio) uses the videoBuffer
        if (type === 'combined') {
          type = 'video';
        }

        segmentObj[type].segments.push(data);
        segmentObj[type].bytes += data.byteLength;

        // Gather any captions into a single array
        if (segment.captions) {
          segmentObj.captions = segmentObj.captions.concat(segment.captions);
        }

        // Gather any metadata into a single array
        if (segment.metadata) {
          segmentObj.metadata = segmentObj.metadata.concat(segment.metadata);
        }

        return segmentObj;
      }, sortedSegments);

      addTextTrackData(this, sortedSegments.captions, sortedSegments.metadata);

      // Merge multiple video and audio segments into one and append
      this.concatAndAppendSegments_(sortedSegments.video, this.videoBuffer_);
      this.concatAndAppendSegments_(sortedSegments.audio, this.audioBuffer_);

      this.pendingBuffers_.length = 0;

      // We are no longer in the internal "updating" state
      this.bufferUpdating_ = false;
    },
    /**
     * Combind all segments into a single Uint8Array and then append them
     * to the destination buffer
     */
    concatAndAppendSegments_: function(segmentObj, destinationBuffer) {
      var
        offset = 0,
        tempBuffer;

      if (segmentObj.bytes) {
        tempBuffer = new Uint8Array(segmentObj.bytes);

        // Combine the individual segments into one large typed-array
        segmentObj.segments.forEach(function (segment) {
          tempBuffer.set(segment, offset);
          offset += segment.byteLength;
        });

        destinationBuffer.appendBuffer(tempBuffer);
      }
    },
    // abort any sourceBuffer actions and throw out any un-appended data
    abort: function() {
      if (this.videoBuffer_) {
        this.videoBuffer_.abort();
      }
      if (this.audioBuffer_) {
        this.audioBuffer_.abort();
      }
      if (this.transmuxer_) {
        this.transmuxer_.postMessage({action: 'reset'});
      }
      this.pendingBuffers_.length = 0;
      this.bufferUpdating_ = false;
    }
  });

  // -----
  // Flash
  // -----

  videojs.FlashMediaSource = videojs.extend(EventTarget, {
    constructor: function(){
      var self = this;
      this.sourceBuffers = [];
      this.readyState = 'closed';

      this.on(['sourceopen', 'webkitsourceopen'], function(event){
        // find the swf where we will push media data
        this.swfObj = document.getElementById(event.swfId);
        this.player_ = videojs(this.swfObj.parentNode);
        this.tech_ = this.swfObj.tech;
        this.readyState = 'open';

        this.tech_.on('seeking', function() {
          var i = self.sourceBuffers.length;
          while (i--) {
            self.sourceBuffers[i].abort();
          }
        });

        // trigger load events
        if (this.swfObj) {
          this.swfObj.vjs_load();
        }
      });
    },
    addSeekableRange_: function() {
      // intentional no-op
    }
  });

  /**
   * The maximum size in bytes for append operations to the video.js
   * SWF. Calling through to Flash blocks and can be expensive so
   * tuning this parameter may improve playback on slower
   * systems. There are two factors to consider:
   * - Each interaction with the SWF must be quick or you risk dropping
   * video frames. To maintain 60fps for the rest of the page, each append
   * must not  take longer than 16ms. Given the likelihood that the page
   * will be executing more javascript than just playback, you probably
   * want to aim for less than 8ms. We aim for just 4ms.
   * - Bigger appends significantly increase throughput. The total number of
   * bytes over time delivered to the SWF must exceed the video bitrate or
   * playback will stall.
   *
   * We adaptively tune the size of appends to give the best throughput
   * possible given the performance of the system. To do that we try to append
   * as much as possible in TIME_PER_TICK and while tuning the size of appends
   * dynamically so that we only append about 4-times in that 4ms span.
   *
   * The reason we try to keep the number of appends around four is due to
   * externalities such as Flash load and garbage collection that are highly
   * variable and having 4 iterations allows us to exit the loop early if
   * an iteration takes longer than expected.
   */

  videojs.FlashMediaSource.TIME_BETWEEN_TICKS = Math.floor(1000 / 480);
  videojs.FlashMediaSource.TIME_PER_TICK = Math.floor(1000 / 240);
  videojs.FlashMediaSource.BYTES_PER_CHUNK = 1 * 1024; // 1kb
  videojs.FlashMediaSource.MIN_CHUNK = 1024;
  videojs.FlashMediaSource.MAX_CHUNK = 1024 * 1024;

  // create a new source buffer to receive a type of media data
  videojs.FlashMediaSource.prototype.addSourceBuffer = function(type){
    var sourceBuffer;

    // if this is an FLV type, we'll push data to flash
    if (type.indexOf('video/mp2t') === 0) {
      // Flash source buffers
      sourceBuffer = new videojs.FlashSourceBuffer(this);
    } else {
      throw new Error('NotSupportedError (Video.js)');
    }

    this.sourceBuffers.push(sourceBuffer);
    return sourceBuffer;
  };

  /**
   * Set or return the presentation duration.
   * @param value {double} the duration of the media in seconds
   * @param {double} the current presentation duration
   * @see http://www.w3.org/TR/media-source/#widl-MediaSource-duration
   */
  try {
    Object.defineProperty(videojs.FlashMediaSource.prototype, 'duration', {
      get: function(){
        if (!this.swfObj) {
          return NaN;
        }
        // get the current duration from the SWF
        return this.swfObj.vjs_getProperty('duration');
      },
      set: function(value){
        var
          i,
          oldDuration = this.swfObj.vjs_getProperty('duration');

        this.swfObj.vjs_setProperty('duration', value);

        if (value < oldDuration) {
          // In MSE, this triggers the range removal algorithm which causes
          // an update to occur
          for (i = 0; i < this.sourceBuffers.length; i++) {
            this.sourceBuffers[i].remove(value, oldDuration);
          }
        }

        return value;
      }
    });
  } catch (e) {
    // IE8 throws if defineProperty is called on a non-DOM node. We
    // don't support IE8 but we shouldn't throw an error if loaded
    // there.
    videojs.FlashMediaSource.prototype.duration = NaN;
  }

  /**
   * Signals the end of the stream.
   * @param error {string} (optional) Signals that a playback error
   * has occurred. If specified, it must be either "network" or
   * "decode".
   * @see https://w3c.github.io/media-source/#widl-MediaSource-endOfStream-void-EndOfStreamError-error
   */
  videojs.FlashMediaSource.prototype.endOfStream = function(error){
    if (error === 'network') {
      // MEDIA_ERR_NETWORK
      this.tech_.error(2);
    } else if (error === 'decode') {
      // MEDIA_ERR_DECODE
      this.tech_.error(3);
    }
    if (this.readyState !== 'ended') {
      this.readyState = 'ended';
      this.swfObj.vjs_endOfStream();
    }
  };

  // store references to the media sources so they can be connected
  // to a video element (a swf object)
  videojs.mediaSources = {};
  // provide a method for a swf object to notify JS that a media source is now open
  videojs.MediaSource.open = function(msObjectURL, swfId){
    var mediaSource = videojs.mediaSources[msObjectURL];

    if (mediaSource) {
      mediaSource.trigger({
        type: 'sourceopen',
        swfId: swfId
      });
    } else {
      throw new Error('Media Source not found (Video.js)');
    }
  };

  scheduleTick = function(func) {
    // Chrome doesn't invoke requestAnimationFrame callbacks
    // in background tabs, so use setTimeout.
    window.setTimeout(func, videojs.FlashMediaSource.TIME_BETWEEN_TICKS);
  };

  // Source Buffer
  videojs.FlashSourceBuffer = videojs.extend(EventTarget, {

    constructor: function(mediaSource){
      var
        encodedHeader,
        self = this;

      // Start off using the globally defined value but refine
      // as we append data into flash
      this.chunkSize_ = videojs.FlashMediaSource.BYTES_PER_CHUNK;

      // byte arrays queued to be appended
      this.buffer_ = [];

      // the total number of queued bytes
      this.bufferSize_ =  0;

      // to be able to determine the correct position to seek to, we
      // need to retain information about the mapping between the
      // media timeline and PTS values
      this.basePtsOffset_ = NaN;

      this.mediaSource = mediaSource;

      // indicates whether the asynchronous continuation of an operation
      // is still being processed
      // see https://w3c.github.io/media-source/#widl-SourceBuffer-updating
      this.updating = false;
      this.timestampOffset_ = 0;

      // TS to FLV transmuxer
      this.segmentParser_ = new muxjs.flv.Transmuxer();
      this.segmentParser_.on('data', this.receiveBuffer_.bind(this));
      encodedHeader = window.btoa(String.fromCharCode.apply(null, Array.prototype.slice.call(this.segmentParser_.getFlvHeader())));
      this.mediaSource.swfObj.vjs_appendBuffer(encodedHeader);

      Object.defineProperty(this, 'timestampOffset', {
        get: function() {
          return this.timestampOffset_;
        },
        set: function(val) {
          if (typeof val === 'number' && val >= 0) {
            this.timestampOffset_ = val;
            this.segmentParser_ = new muxjs.flv.Transmuxer();
            this.segmentParser_.on('data', this.receiveBuffer_.bind(this));
            // We have to tell flash to expect a discontinuity
            this.mediaSource.swfObj.vjs_discontinuity();
            // the media <-> PTS mapping must be re-established after
            // the discontinuity
            this.basePtsOffset_ = NaN;
          }
        }
      });

      Object.defineProperty(this, 'buffered', {
        get: function() {
          return videojs.createTimeRanges(this.mediaSource.swfObj.vjs_getProperty('buffered'));
        }
      });

      // On a seek we remove all text track data since flash has no concept
      // of a buffered-range and everything else is reset on seek
      this.mediaSource.player_.on('seeked', function() {
        removeCuesFromTrack(0, Infinity, self.metadataTrack_);
        removeCuesFromTrack(0, Infinity, self.inbandTextTrack_);
      });
    },

    // accept video data and pass to the video (swf) object
    appendBuffer: function(bytes){
      var error, self = this;

      if (this.updating) {
        error = new Error('SourceBuffer.append() cannot be called ' +
                          'while an update is in progress');
        error.name = 'InvalidStateError';
        error.code = 11;
        throw error;
      }

      this.updating = true;
      this.mediaSource.readyState = 'open';
      this.trigger({ type: 'update' });

      var chunk = 512 * 1024;
      var i = 0;
      (function chunkInData() {
        self.segmentParser_.push(bytes.subarray(i, i + chunk));
        i += chunk;
        if (i < bytes.byteLength) {
          scheduleTick(chunkInData);
        } else {
          scheduleTick(self.segmentParser_.flush.bind(self.segmentParser_));
        }
      })();
    },

    // reset the parser and remove any data queued to be sent to the swf
    abort: function() {
      this.buffer_ = [];
      this.bufferSize_ = 0;
      this.mediaSource.swfObj.vjs_abort();

      // report any outstanding updates have ended
      if (this.updating) {
        this.updating = false;
        this.trigger({ type: 'updateend' });
      }
    },

    // Flash cannot remove ranges already buffered in the NetStream
    // but seeking clears the buffer entirely. For most purposes,
    // having this operation act as a no-op is acceptable.
    remove: function(start, end) {
      removeCuesFromTrack(start, end, this.metadataTrack_);
      removeCuesFromTrack(start, end, this.inbandTextTrack_);
      this.trigger({ type: 'update' });
      this.trigger({ type: 'updateend' });
    },

    receiveBuffer_: function(segment) {
      var self = this;

      // create an in-band caption track if one is present in the segment
      createTextTracksIfNecessary(this, this.mediaSource, segment);
      addTextTrackData(this, segment.captions, segment.metadata);

      // Do this asynchronously since convertTagsToData_ can be time consuming
      scheduleTick(function() {
        if (self.buffer_.length === 0) {
          scheduleTick(self.processBuffer_.bind(self));
        }
        var flvBytes = self.convertTagsToData_(segment);
        if (flvBytes) {
          self.buffer_.push(flvBytes);
          self.bufferSize_ += flvBytes.byteLength;
        }
      });
    },

    // append a portion of the current buffer to the SWF
    processBuffer_: function() {
      var
        chunk,
        i,
        length,
        binary,
        b64str,
        startByte = 0,
        appendIterations = 0,
        startTime = +(new Date()),
        appendTime;

      if (!this.buffer_.length) {
        if (this.updating !== false) {
          this.updating = false;
          this.trigger({ type: 'updateend' });
        }
        // do nothing if the buffer is empty
        return;
      }

      do {
        appendIterations++;
        // concatenate appends up to the max append size
        chunk = this.buffer_[0].subarray(startByte, startByte + this.chunkSize_);

        // requeue any bytes that won't make it this round
        if (chunk.byteLength < this.chunkSize_ ||
            this.buffer_[0].byteLength === startByte + this.chunkSize_) {
          startByte = 0;
          this.buffer_.shift();
        } else {
          startByte += this.chunkSize_;
        }

        this.bufferSize_ -= chunk.byteLength;

        // base64 encode the bytes
        binary = '';
        length = chunk.byteLength;
        for (i = 0; i < length; i++) {
          binary += String.fromCharCode(chunk[i]);
        }
        b64str = window.btoa(binary);

        // bypass normal ExternalInterface calls and pass xml directly
        // IE can be slow by default
        this.mediaSource.swfObj.CallFunction('<invoke name="vjs_appendBuffer"' +
                                             'returntype="javascript"><arguments><string>' +
                                             b64str +
                                             '</string></arguments></invoke>');
        appendTime = (new Date()) - startTime;
      } while (this.buffer_.length &&
          appendTime < videojs.FlashMediaSource.TIME_PER_TICK);

      if (this.buffer_.length && startByte) {
        this.buffer_[0] = this.buffer_[0].subarray(startByte);
      }

      if (appendTime >= videojs.FlashMediaSource.TIME_PER_TICK) {
        // We want to target 4 iterations per time-slot so that gives us
        // room to adjust to changes in Flash load and other externalities
        // such as garbage collection while still maximizing throughput
        this.chunkSize_ = Math.floor(this.chunkSize_ * (appendIterations / 4));
      }

      // We also make sure that the chunk-size doesn't drop below 1KB or
      // go above 1MB as a sanity check
      this.chunkSize_ = Math.max(
        videojs.FlashMediaSource.MIN_CHUNK,
        Math.min(this.chunkSize_, videojs.FlashMediaSource.MAX_CHUNK));

      // schedule another append if necessary
      if (this.bufferSize_ !== 0) {
        scheduleTick(this.processBuffer_.bind(this));
      } else {
        this.updating = false;
        this.trigger({ type: 'updateend' });

      }
    },

    // Turns an array of flv tags into a Uint8Array representing the
    // flv data. Also removes any tags that are before the current
    // time so that playback begins at or slightly after the right
    // place on a seek
    convertTagsToData_: function (segmentData) {
      var
        segmentByteLength = 0,
        tech = this.mediaSource.tech_,
        targetPts = 0,
        i, j, segment,
        filteredTags = [],
        tags = this.getOrderedTags_(segmentData);

      // Establish the media timeline to PTS translation if we don't
      // have one already
      if (isNaN(this.basePtsOffset_) && tags.length) {
        this.basePtsOffset_ = tags[0].pts;
      }

      // Trim any tags that are before the end of the end of
      // the current buffer
      if (tech.buffered().length) {
        targetPts = tech.buffered().end(0) - this.timestampOffset;
      }
      // Trim to currentTime if it's ahead of buffered or buffered doesn't exist
      targetPts = Math.max(targetPts, tech.currentTime() - this.timestampOffset);

      targetPts *= 1e3; // PTS values are represented in milliseconds
      targetPts += this.basePtsOffset_;

      // skip tags with a presentation time less than the seek target
      for (i = 0; i < tags.length; i++) {
        if (tags[i].pts >= targetPts) {
          filteredTags.push(tags[i]);
        }
      }

      if (filteredTags.length === 0) {
        return;
      }

      // concatenate the bytes into a single segment
      for (i = 0; i < filteredTags.length; i++) {
        segmentByteLength += filteredTags[i].bytes.byteLength;
      }
      segment = new Uint8Array(segmentByteLength);
      for (i = 0, j = 0; i < filteredTags.length; i++) {
        segment.set(filteredTags[i].bytes, j);
        j += filteredTags[i].bytes.byteLength;
      }

      return segment;
    },

    // assemble the FLV tags in decoder order
    getOrderedTags_: function(segmentData) {
      var
        videoTags = segmentData.tags.videoTags,
        audioTags = segmentData.tags.audioTags,
        tag,
        tags = [];

      while (videoTags.length || audioTags.length) {
        if (!videoTags.length) {
          // only audio tags remain
          tag = audioTags.shift();
        } else if (!audioTags.length) {
          // only video tags remain
          tag = videoTags.shift();
        } else if (audioTags[0].dts < videoTags[0].dts) {
          // audio should be decoded next
          tag = audioTags.shift();
        } else {
          // video should be decoded next
          tag = videoTags.shift();
        }

        tags.push(tag.finalize());
      }

      return tags;
    }
  });

  // URL
  videojs.URL = {
    createObjectURL: function(object){
      var url;

      // use the native MediaSource to generate an object URL
      if (object instanceof videojs.HtmlMediaSource) {
        url = window.URL.createObjectURL(object.mediaSource_);
        object.url_ = url;
        return url;
      }

      // if the object isn't an emulated MediaSource, delegate to the
      // native implementation
      if (!(object instanceof videojs.FlashMediaSource)) {
        url = window.URL.createObjectURL(object);
        object.url_ = url;
        return url;
      }

      // build a URL that can be used to map back to the emulated
      // MediaSource
      url = objectUrlPrefix + urlCount;

      urlCount++;

      // setup the mapping back to object
      videojs.mediaSources[url] = object;

      return url;
    }
  };

})(this, this.muxjs);
