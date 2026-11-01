{{-- Partial: Owner Sidebar Menu - include ini di setiap halaman owner --}}
<span class="sidebar-section-title">Menu Utama</span>
<a href="{{ route('owner.dashboard') }}" class="sidebar-link {{ request()->routeIs('owner.dashboard') ? 'active' : '' }}">
    <i class="bi bi-grid sidebar-icon"></i> Dashboard
</a>

<span class="sidebar-section-title">Laporan</span>
<a href="{{ route('owner.reports.index') }}" class="sidebar-link {{ request()->routeIs('owner.reports.*') && !request('category_slug') ? 'active' : '' }}">
    <i class="bi bi-file-earmark-text sidebar-icon"></i> Semua Laporan
    @php $pc = \App\Models\Report::where('status','pending')->count(); @endphp
    @if($pc) <span class="sidebar-badge">{{ $pc }}</span> @endif
</a>
@foreach(\App\Models\Category::active()->orderBy('urutan')->get() as $cat)
<a href="{{ route('owner.reports.index', ['category_slug' => $cat->slug]) }}" class="sidebar-link {{ request('category_slug') == $cat->slug ? 'active' : '' }}">
    <i class="{{ $cat->icon }} sidebar-icon"></i> {{ $cat->name }}
</a>
@endforeach

<span class="sidebar-section-title">Manajemen</span>
<a href="{{ route('owner.categories.index') }}" class="sidebar-link {{ request()->routeIs('owner.categories.*') ? 'active' : '' }}">
    <i class="bi bi-tags sidebar-icon"></i> Kategori Laporan
</a>
<a href="{{ route('owner.tenants.index') }}" class="sidebar-link {{ request()->routeIs('owner.tenants.*') && !request()->routeIs('owner.tenants.history') ? 'active' : '' }}">
    <i class="bi bi-people sidebar-icon"></i> Data Penghuni
</a>
<a href="{{ route('owner.rooms.index') }}" class="sidebar-link {{ request()->routeIs('owner.rooms.*') ? 'active' : '' }}">
    <i class="bi bi-door-open sidebar-icon"></i> Data Kamar
</a>
<a href="{{ route('owner.tenants.history') }}" class="sidebar-link {{ request()->routeIs('owner.tenants.history') ? 'active' : '' }}">
    <i class="bi bi-clock-history sidebar-icon"></i> Riwayat Penghuni
</a>

<span class="sidebar-section-title">Laporan & Analitik</span>
<a href="{{ route('owner.statistics.index') }}" class="sidebar-link {{ request()->routeIs('owner.statistics.*') ? 'active' : '' }}">
    <i class="bi bi-bar-chart sidebar-icon"></i> Laporan & Statistik
</a>

<span class="sidebar-section-title">Akun</span>
<a href="{{ route('owner.profile.edit') }}" class="sidebar-link {{ request()->routeIs('owner.profile.*') ? 'active' : '' }}">
    <i class="bi bi-person-circle sidebar-icon"></i> Pengaturan Profil
</a>
