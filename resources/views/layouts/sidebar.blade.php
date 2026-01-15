<!-- Mobile Overlay -->
<div x-show="sidebarOpen" 
     x-transition:enter="transition-opacity ease-linear duration-300"
     x-transition:enter-start="opacity-0"
     x-transition:enter-end="opacity-100"
     x-transition:leave="transition-opacity ease-linear duration-300"
     x-transition:leave-start="opacity-100"
     x-transition:leave-end="opacity-0"
     class="fixed inset-0 bg-gray-900/50 backdrop-blur-sm z-40 lg:hidden"
     @click="sidebarOpen = false">
</div>

<!-- Sidebar -->
<aside :class="[sidebarOpen ? 'translate-x-0' : '-translate-x-full', collapsed ? 'lg:w-20' : 'lg:w-72']"
       class="fixed inset-y-0 left-0 z-50 w-72 transform transition-all duration-300 ease-in-out lg:translate-x-0 lg:relative lg:inset-auto shrink-0">
    
    <div class="flex flex-col h-full bg-gradient-to-b from-slate-900 via-slate-800 to-slate-900 shadow-2xl">
        <!-- Logo Section -->
        <div class="flex items-center justify-between h-16 px-4 border-b border-slate-700/50">
            <a href="{{ route('dashboard') }}" class="flex items-center gap-3">
                <div class="flex items-center justify-center w-10 h-10 rounded-xl bg-gradient-to-br from-blue-500 to-cyan-400 shadow-lg shadow-blue-500/30">
                    <svg class="w-6 h-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                    </svg>
                </div>
                <span x-show="!collapsed" x-transition:enter="transition-opacity duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" class="text-xl font-bold text-white tracking-tight">
                    Alu<span class="text-cyan-400">ERP</span>
                </span>
            </a>
            
            <!-- Collapse Toggle (Desktop) -->
            <button @click="collapsed = !collapsed" class="hidden lg:flex items-center justify-center w-8 h-8 rounded-lg text-slate-400 hover:text-white hover:bg-slate-700/50 transition-colors">
                <svg :class="collapsed ? 'rotate-180' : ''" class="w-5 h-5 transition-transform duration-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 19l-7-7 7-7m8 14l-7-7 7-7" />
                </svg>
            </button>
            
            <!-- Close Button (Mobile) -->
            <button @click="sidebarOpen = false" class="lg:hidden flex items-center justify-center w-8 h-8 rounded-lg text-slate-400 hover:text-white hover:bg-slate-700/50 transition-colors">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        <!-- Navigation -->
        <nav class="flex-1 overflow-y-auto py-6 px-3 space-y-1">
            <!-- Main Section -->
            <div x-show="!collapsed" class="px-3 mb-3">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Principal</span>
            </div>
            
            <x-sidebar-link-new :href="route('dashboard')" :active="request()->routeIs('dashboard')" icon="home" :collapsed="false">
                Tableau de bord
            </x-sidebar-link-new>

            <!-- Business Section -->
            <div x-show="!collapsed" class="px-3 mt-6 mb-3">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Gestion</span>
            </div>

            <x-sidebar-link-new :href="route('clients.index')" :active="request()->routeIs('clients.*')" icon="users" :collapsed="false">
                Clients
            </x-sidebar-link-new>

            <x-sidebar-link-new :href="route('suppliers.index')" :active="request()->routeIs('suppliers.*')" icon="truck" :collapsed="false">
                Fournisseurs
            </x-sidebar-link-new>

            <x-sidebar-link-new :href="route('purchases.index')" :active="request()->routeIs('purchases.*')" icon="shopping-cart" :collapsed="false">
                Achats
            </x-sidebar-link-new>

            <!-- Sales Section -->
            <div x-show="!collapsed" class="px-3 mt-6 mb-3">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Ventes</span>
            </div>

            <x-sidebar-link-new :href="route('pos.index')" :active="request()->routeIs('pos.*')" icon="monitor" :collapsed="false">
                Terminal POS
            </x-sidebar-link-new>

            <x-sidebar-link-new :href="route('payments.index')" :active="request()->routeIs('payments.*')" icon="credit-card" :collapsed="false">
                Paiements
            </x-sidebar-link-new>

            <x-sidebar-link-new :href="route('invoices.index')" :active="request()->routeIs('invoices.*')" icon="file-text" :collapsed="false">
                Factures
            </x-sidebar-link-new>

            <x-sidebar-link-new :href="route('quotes.index')" :active="request()->routeIs('quotes.*')" icon="file" :collapsed="false">
                Devis
            </x-sidebar-link-new>

            <!-- Analytics Section -->
            <div x-show="!collapsed" class="px-3 mt-6 mb-3">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Analyses</span>
            </div>

            <x-sidebar-link-new :href="route('reports.index')" :active="request()->routeIs('reports.*')" icon="bar-chart" :collapsed="false">
                Rapports
            </x-sidebar-link-new>
        </nav>

        <!-- User Profile Section -->
        <div class="p-4 border-t border-slate-700/50">
            <div :class="collapsed ? 'justify-center' : 'justify-between'" class="flex items-center gap-3">
                <div class="flex items-center gap-3">
                    <div class="relative">
                        <img class="h-10 w-10 rounded-xl object-cover ring-2 ring-slate-700" 
                             src="https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->name) }}&color=7F9CF5&background=1e293b&bold=true" 
                             alt="{{ Auth::user()->name }}">
                        <span class="absolute bottom-0 right-0 w-3 h-3 bg-green-500 border-2 border-slate-800 rounded-full"></span>
                    </div>
                    <div x-show="!collapsed" x-transition class="min-w-0">
                        <p class="text-sm font-semibold text-white truncate">
                            {{ Auth::user()->name }}
                        </p>
                        <p class="text-xs text-cyan-400 truncate capitalize">
                            {{ Auth::user()->role }}
                        </p>
                    </div>
                </div>
                <a x-show="!collapsed" href="{{ route('profile.edit') }}" class="p-2 rounded-lg text-slate-400 hover:text-white hover:bg-slate-700/50 transition-colors">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                </a>
            </div>
            
            <!-- Logout Button -->
            <form method="POST" action="{{ route('logout') }}" x-show="!collapsed" class="mt-4">
                @csrf
                <button type="submit" class="w-full flex items-center justify-center gap-2 px-4 py-2.5 text-sm font-medium text-slate-300 bg-slate-800/50 hover:bg-red-500/20 hover:text-red-400 rounded-xl transition-all duration-200">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                    </svg>
                    Déconnexion
                </button>
            </form>
        </div>
    </div>
</aside>
