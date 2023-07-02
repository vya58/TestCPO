<nav class="mt-2">
    <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
        <!-- Add icons to the links using the .nav-icon class
               with font-awesome or any other icon font library -->

        <li class="nav-header">ADMIN PANEL</li>
        <li class="nav-item">
            <a href="{{ route('event.index') }}" class="nav-link">
                <i class="nav-icon fas fa-sharp fa-regular fa-bars"></i>
                <p>
                    Все события
                    <span class="badge badge-info right">{{ $allEvents->total() }}</span>
                </p>
            </a>

        </li>
        <li class="nav-item">
            <a href="{{ route('event.self') }}" class="nav-link">
                <i class="nav-icon fas fa-sharp fa-regular fa-bars"></i>
                <p>
                    Мои события
                    <span class="badge badge-info right">{{ $myEvents->count() }}</span>
                </p>
            </a>
        </li>
    </ul>
</nav>
