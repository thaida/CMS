
<div class="sidebar-inner">
	<div class="si-inner">
		<div class="profile-menu">
			<a href="">
				<div class="profile-pic">
					<img src="img/profile-pics/1.jpg" alt="">
				</div>

				<div class="profile-info">
					{{ auth()->user()->username }} <i class="md md-arrow-drop-down"></i>
				</div>
			</a>

			<ul class="main-menu">
				<li><a href=""><i class="md md-person"></i> View Profile</a></li>
				<li><a href=""><i class="md md-settings-input-antenna"></i> Privacy
						Settings</a></li>
				<li><a href=""><i class="md md-settings"></i> Settings</a></li>
				<li><a href="{{url('auth/logout')}}"><i class="md md-history"></i>
						Logout</a></li>
			</ul>
		</div>

		<ul class="main-menu">
			<li class="active"><a href="{{url('/admin')}}"><i class="md md-home"></i>
					Home</a>
			</li>
			<li><a href="{!! route('medias') !!}"><i
					class="md md-format-underline"></i> Media</a>
			</li>
			<li class="sub-menu"><a href=""><i class="md md-now-widgets"></i>
					Users</a>

				<ul>
					<li><a class="active" href="{!! url('user') !!}">See All</a></li>
					<li><a class="active" href="{!! url('user/create') !!}">{{
							trans('back/admin.add') }}</a></li>
					<li><a class="active" href="{!! url('user/roles') !!}">{{
							trans('back/roles.roles') }}</a></li>
				</ul>
			</li>
			<li class="sub-menu"><a href=""><i class="md md-view-list"></i> Films</a>

				<ul>
					<li><a href="{!! url('cat') !!}">Danh muc</a></li>
					<li><a href="{!! url('subcat') !!}">Danh muc con</a></li>
					<li><a href="{!! url('film') !!}">Film</a></li>
					<li><a href="{!! url('music') !!}">Nhac</a></li>
					<li><a href="{!! url('banner') !!}">Banner</a></li>
					<li><a href="{!! url('category/create') !!}">Danh muc</a></li>
				</ul>
			</li>

		</ul>
	</div>
</div>
