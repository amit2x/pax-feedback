<nav class="paf-sidebar-nav">
    <a href="{{ route('admin.dashboard') }}"
        class="paf-sidebar-link {{ request()->routeIs('admin.dashboard') ? 'is-active' : '' }}">
        <span class="paf-sidebar-icon">📊</span>
        <span>Dashboard</span>
    </a>

    @can('feedback.view')
    <a href="{{ route('admin.feedback.index') }}"
        class="paf-sidebar-link {{ request()->routeIs('admin.feedback.*') ? 'is-active' : '' }}">
        <span class="paf-sidebar-icon">💬</span>
        <span>Feedback</span>
    </a>
    @endcan

    @can('categories.view')
    <a href="{{ route('admin.categories.index') }}"
        class="paf-sidebar-link {{ request()->routeIs('admin.categories.*') ? 'is-active' : '' }}">
        <span class="paf-sidebar-icon">🗂️</span>
        <span>Categories</span>
    </a>
    @endcan

    @can('locations.view')
    <a href="{{ route('admin.locations.index') }}"
        class="paf-sidebar-link {{ request()->routeIs('admin.locations.*') ? 'is-active' : '' }}">
        <span class="paf-sidebar-icon">📍</span>
        <span>Locations</span>
    </a>
    @endcan

    @can('qr.view')
    <a href="{{ route('admin.qr.index') }}"
        class="paf-sidebar-link {{ request()->routeIs('admin.qr.*') ? 'is-active' : '' }}">
        <span class="paf-sidebar-icon">🔳</span>
        <span>QR Codes</span>
    </a>
    @endcan

    @can('departments.view')
    <a href="{{ route('admin.departments.index') }}"
        class="paf-sidebar-link {{ request()->routeIs('admin.departments.*') ? 'is-active' : '' }}">
        <span class="paf-sidebar-icon">🏢</span>
        <span>Departments</span>
    </a>
    @endcan

    @can('reports.view')
    <a href="{{ route('admin.reports.index') }}"
        class="paf-sidebar-link {{ request()->routeIs('admin.reports.*') ? 'is-active' : '' }}">
        <span class="paf-sidebar-icon">📈</span>
        <span>Reports</span>
    </a>
    @endcan

    @can('audit.view')
    <a href="{{ route('admin.audit.index') }}"
        class="paf-sidebar-link {{ request()->routeIs('admin.audit.*') ? 'is-active' : '' }}">
        <span class="paf-sidebar-icon">📜</span>
        <span>Audit Logs</span>
    </a>
    @endcan

    @can('users.manage')
    <a href="{{ route('admin.users.index') }}"
        class="paf-sidebar-link {{ request()->routeIs('admin.users.*') ? 'is-active' : '' }}">
        <span class="paf-sidebar-icon">👥</span>
        <span>Users</span>
    </a>
    @endcan
</nav>
