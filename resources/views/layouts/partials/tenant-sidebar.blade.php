<span class="sidebar-section-title">Menu Utama</span>
<a href="{{ route('tenant.dashboard') }}" class="sidebar-link {{ request()->routeIs('tenant.dashboard') ? 'active' : '' }}">
    <i class="bi bi-grid sidebar-icon"></i> Dashboard
</a>

<span class="sidebar-section-title">Laporan</span>
<a href="{{ route('tenant.reports.index') }}" class="sidebar-link {{ request()->routeIs('tenant.reports.index') ? 'active' : '' }}">
    <i class="bi bi-card-list sidebar-icon"></i> Laporan Saya
    @php
        $pendingMyReports = \App\Models\Report::where('pengguna_id', auth()->id())->whereIn('status', ['pending', 'menunggu'])->count();
    @endphp
    @if($pendingMyReports > 0)
        <span class="sidebar-badge">{{ $pendingMyReports }}</span>
    @endif
</a>
<a href="{{ route('tenant.reports.create') }}" class="sidebar-link {{ request()->routeIs('tenant.reports.create') ? 'active' : '' }}">
    <i class="bi bi-plus-circle sidebar-icon"></i> Buat Laporan Baru
</a>

<span class="sidebar-section-title">Akun</span>
<a href="{{ route('tenant.profile.edit') }}" class="sidebar-link {{ request()->routeIs('tenant.profile.*') ? 'active' : '' }}">
    <i class="bi bi-person-circle sidebar-icon"></i> Profil Saya
</a>
