<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Sistema de Tickets</title>
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <link href="{{ asset('css/layout.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/bootstrap-icons.css') }}">
    <script src="{{ asset('js/bootstrap.bundle.min.js') }}"></script>
    <link href="{{ asset('css/select2.min.css') }}" rel="stylesheet">

    <link rel="shortcut icon" href="{{ asset('images/raton.png') }}" type="image/png">
    <link rel="icon" href="{{ asset('images/raton.png') }}" type="image/png">

    <style>
    .new-tickets-badge {
        position: absolute;
        top: 5px;
        right: 15px;
        color: white;
        border-radius: 50%;
        width: 22px;
        height: 22px;
        font-size: 0.75rem;
        display: flex;
        align-items: center;
        justify-content: center;
        animation: pulse 2s infinite;
        cursor: pointer;
    }
    
    #newTicketsBadge {
        background: #dc3545;
    }
    
    #assignedTicketsBadge {
        background: #ffc107;
        color: #000;
    }
    
    @keyframes pulse {
        0% { transform: scale(1); }
        50% { transform: scale(1.1); }
        100% { transform: scale(1); }
    }
        
        .nav-item {
            position: relative;
        }
        
        .global-toast {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 99999;
        }
        
        .toast-notification-container {
            position: fixed;
            top: 80px;
            right: 20px;
            z-index: 99999;
            max-width: 350px;
        }
        
        .notification-toast {
            background: white;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            margin-bottom: 10px;
            overflow: hidden;
            animation: slideInRight 0.3s ease-out;
            border-left: 5px solid #4e73df;
        }
        
        @keyframes slideInRight {
            from {
                transform: translateX(100%);
                opacity: 0;
            }
            to {
                transform: translateX(0);
                opacity: 1;
            }
        }
        
        .notification-header {
            background: linear-gradient(135deg, #4e73df 0%, #224abe 100%);
            color: white;
            padding: 12px 15px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        
        .notification-body {
            padding: 15px;
            background: white;
        }
        
        .notification-title {
            font-weight: 600;
            font-size: 1rem;
            margin: 0;
        }
        
        .notification-message {
            font-size: 0.9rem;
            color: #6c757d;
            margin: 8px 0 0 0;
        }
        
        .notification-time {
            font-size: 0.8rem;
            color: #999;
            margin-top: 5px;
        }
        
        .notification-icon {
            font-size: 1.2rem;
            margin-right: 8px;
        }
        
        .notification-close {
            background: none;
            border: none;
            color: white;
            opacity: 0.7;
            cursor: pointer;
            font-size: 1.2rem;
            padding: 0;
            line-height: 1;
        }
        
        .notification-close:hover {
            opacity: 1;
        }
        
        .notification-badge {
            display: inline-block;
            background: #ff4757;
            color: white;
            border-radius: 50%;
            width: 20px;
            height: 20px;
            text-align: center;
            line-height: 20px;
            font-size: 0.7rem;
            margin-left: 5px;
        }
    </style>
    
    @stack('scripts')
    @stack('styles')
</head>

<body>
    <div class="toast-notification-container" id="notificationContainer"></div>
        <nav class="top-navbar navbar navbar-expand">
        <div class="container-fluid">
            <button class="btn text-light" id="sidebarToggle">
                <i class="bi bi-list"></i>
                Departamento de Informática
            </button>

            <div class="navbar-nav ms-auto">
                @auth
                <span class="nav-link text-light">
                    <i class="bi bi-person-fill me-1"></i>
                    Hola, {{ auth()->user()->email }}
                </span>
                @endauth
            </div>
        </div>
    </nav>

    <!-- Sidebar -->
    <div class="sidebar text-white" id="sidebar">
        <div class="sidebar-header">
            <img src="{{ asset('images/logodif.jpg') }}" alt="Menú" class="sidebar-logo">
        </div>
        <div class="p-3 d-flex flex-column" style="height: calc(100% - 100px);">
            <ul class="nav flex-column">
                <li class="nav-item">
                    <a class="nav-link text-white @if(Request::is('/')) active @endif" href="{{ url('/') }}">
                        <i class="bi bi-house-door me-2"></i> Inicio
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-white @if(Request::is('mis-solicitudes*')) active @endif" href="{{ route('mis_solicitudes') }}">
                        <i class="bi bi-ticket-detailed me-2"></i> Mis solicitudes
                    </a>
                </li>

                @guest
                <li class="nav-item">
                    <a class="nav-link text-white @if(Request::is('admin*') || Request::is('administrador*')) active @endif" href="{{ route('login') }}">
                        <i class="bi bi-shield-lock me-2"></i> Administrador
                    </a>
                </li>
                @endguest

                @auth
                @can('acceso administrador')
                <p></p>
                <h6>ADMINISTRACIÓN:</h6>
                <div class="my-2" style="border-top: 2px solid rgba(255, 255, 255, 0.5);"></div>

                @can('ver tickets')
                <li class="nav-item" id="admin-tickets-menu-item">
                    <a class="nav-link text-white @if(Request::is('admin/admin_solicitudes')) active @endif" href="{{ route('admin.admin_solicitudes') }}">
                        <i class="bi bi-ticket-detailed me-2"></i> Administrar Solicitudes

                        {{-- BADGE PARA AMBOS ROLES --}}
                        <span id="newTicketsBadge" class="new-tickets-badge" style="display: none;">
                            <span id="newTicketsCount">0</span>
                        </span>

                        {{-- BADGE ESPECÍFICO PARA SOPORTE (opcional, si quieres diferenciar) --}}
                        <span id="assignedTicketsBadge" class="new-tickets-badge" style="display: none; background: #ffc107;">
                            <span id="assignedTicketsCount">0</span>
                        </span>
                    </a>
                </li>
                @endcan

                @can('ver usuarios')
                <li class="nav-item">
                    <a class="nav-link text-white @if(Request::is('admin/usuarios')) active @endif" href="{{ route('admin.usuarios') }}">
                        <i class="bi bi-people me-2"></i> Usuarios
                    </a>
                </li>
                @endcan

                @can('ver personal soporte')
                <li class="nav-item">
                    <a class="nav-link text-white @if(Request::is('admin/soporte')) active @endif" href="{{ route('admin.soporte') }}">
                        <i class="bi bi-headset me-2"></i> Personal de soporte
                    </a>
                </li>
                @endcan

                @can('ver edificios')
                <li class="nav-item">
                    <a class="nav-link text-white @if(Request::is('admin/edificios*')) active @endif" href="{{ route('admin.edificios') }}">
                        <i class="bi bi-building me-2"></i> Edificios
                    </a>
                </li>
                @endcan
                @can('ver departamentos')
                <li class="nav-item">
                    <a class="nav-link text-white @if(Request::is('admin/departamentos*')) active @endif" href="{{ route('admin.departamentos') }}">
                        <i class="bi bi-diagram-3 me-2"></i> Departamentos
                    </a>
                </li>
                @endcan

                @can('ver empleados')
                <li class="nav-item">
                    <a class="nav-link text-white @if(Request::is('admin/empleados*')) active @endif" href="{{ route('admin.empleados') }}">
                        <i class="bi bi-person-badge me-2"></i> Empleados
                    </a>
                </li>
                @endcan

                @can('ver reportes')
                <li class="nav-item">
                    <a class="nav-link text-white @if(Request::is('admin/reportes')) active @endif" href="{{ route('admin.reportes') }}">
                        <i class="bi bi-clipboard2-data me-2"></i> Reportes
                    </a>
                </li>
                @endcan

                @role('superadmin')
                <li class="nav-item">
                    <a class="nav-link text-white @if(Request::is('admin/gestion-permisos*')) active @endif" href="{{ route('admin.permisos.manager') }}">
                        <i class="bi bi-shield-lock me-2"></i> Roles y Permisos
                    </a>
                </li>
                @endrole
                @endcan
                @endauth
            </ul>

            @auth
            <div class="mt-auto">
                <ul class="nav flex-column">
                    <li class="nav-item">
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <a class="nav-link text-white" href="{{ route('logout') }}" onclick="event.preventDefault(); this.closest('form').submit();">
                                <i class="bi bi-box-arrow-right me-2"></i> Cerrar sesión
                            </a>
                        </form>
                    </li>
                </ul>
            </div>
            @endauth
        </div>
    </div>

    <div class="main-content" id="mainContent">
        <div style="height: 56px;"></div>
        @yield('content')
    </div>
<audio id="audioNuevo" preload="none" style="display: none;">
<source src="{{ asset('audios/audio_nuevos.mp3') }}" type="audio/mpeg">
</audio>
<audio id="audioAsignado" preload="none" style="display: none;">
    <source src="{{ asset('audios/audio_asignado.mp3') }}" type="audio/mpeg">
</audio>

    <script src="{{ asset('js/jquery-3.6.0.min.js') }}"></script>
    <script src="{{ asset('js/app.js') }}"></script>
    <script src="{{ asset('js/select2.min.js') }}"></script>
    <script src="{{ asset('js/es.js') }}"></script>
<script>
$(document).ready(function() {
    const hasTicketPermission = {{ auth()->check() && auth()->user()->can('ver tickets') ? 'true' : 'false' }};
    if (!hasTicketPermission) return;

    @auth
    const userPermissions = @json(auth()->user()->getAllPermissions()->pluck('name')->toArray());
    const isSupport = userPermissions.includes('notificaciones tickets asignados');
    const isAdmin = userPermissions.includes('notificaciones tickets nuevos');
    @endauth

    let lastTicketCount = 0;
    let lastAssignedCount = 0;
    let originalTitle = document.title.replace(/^\(\d+\)\s*/, '');

    // 🔊 Activar audio tras primer click
    $(document).one('click', function() {
        try {
            const audioNuevo = document.getElementById('audioNuevo');
            const audioAsignado = document.getElementById('audioAsignado');
            if (audioNuevo) audioNuevo.load();
            if (audioAsignado) audioAsignado.load();
            console.log('🔊 Audios activados');
        } catch (e) {}
    });

    function updateTitle(count) {
        if (count > 0) {
            document.title = `(${count}) ${originalTitle}`;
        } else {
            document.title = originalTitle;
        }
    }

    function restoreTitle() {
        document.title = originalTitle;
    }

    function playMp3Sound(type) {
        try {
            const audioElement = type === 'admin' 
                ? document.getElementById('audioNuevo') 
                : document.getElementById('audioAsignado');

            if (audioElement) {
                audioElement.currentTime = 0;
                audioElement.volume = 0.8;
                audioElement.play().catch(() => {});
            }
        } catch (e) {}
    }

    function showBrowserNotification(type, count) {
        if (!("Notification" in window)) return;

        if (Notification.permission === "default") {
            Notification.requestPermission();
        }

        if (Notification.permission === "granted") {
            let title, body;

            if (type === 'admin') {
                title = '🎫 Nuevo Ticket';
                body = `Tienes ${count} nuevo(s) ticket(s)`;
            } else {
                title = '👨‍💻 Ticket Asignado';
                body = `Se te ha asignado ${count} nuevo(s) ticket(s)`;
            }

            const notification = new Notification(title, {
                body: body,
                icon: '{{ asset("images/raton.png") }}',
                tag: 'ticket-notification'
            });

            notification.onclick = function() {
                window.focus();
                if (type === 'admin') {
                    window.location.href = '{{ route("admin.admin_solicitudes") }}?status=nuevo';
                } else {
                    window.location.href = '{{ route("admin.admin_solicitudes") }}?status=atendiendo';
                }
                notification.close();
            };

            setTimeout(() => notification.close(), 6000);
        }
    }

    function checkNotifications() {
        let url = '';
        let badgeType = '';

        if (isAdmin) {
            url = '{{ route("admin.notifications.new-tickets-count") }}';
            badgeType = 'admin';
        } else if (isSupport) {
            url = '{{ route("admin.notifications.assigned-count") }}';
            badgeType = 'support';
        } else {
            return;
        }

        $.ajax({
            url: url,
            type: 'GET',
            headers: { 'X-Requested-With': 'XMLHttpRequest' },
            success: function(response) {
                if (!response.success) return;

                const currentCount = response.count || 0;
                const previousCount = badgeType === 'admin' ? lastTicketCount : lastAssignedCount;

                if (currentCount > previousCount) {
                    const diff = currentCount - previousCount;

                    playMp3Sound(badgeType);

                    if (document.hidden) {
                        showBrowserNotification(badgeType, diff);
                    }
                }

                updateTitle(currentCount);

                // 🏷️ Actualizar badges
                if (badgeType === 'admin') {
                    if (currentCount > 0) {
                        $('#newTicketsCount').text(currentCount);
                        $('#newTicketsBadge').show();
                        $('#assignedTicketsBadge').hide();
                    } else {
                        $('#newTicketsBadge').hide();
                    }
                    lastTicketCount = currentCount;
                } else {
                    if (currentCount > 0) {
                        $('#assignedTicketsCount').text(currentCount);
                        $('#assignedTicketsBadge').show();
                        $('#newTicketsBadge').hide();
                    } else {
                        $('#assignedTicketsBadge').hide();
                    }
                    lastAssignedCount = currentCount;
                }
            },
            error: function() {
                restoreTitle();
                $('#newTicketsBadge').hide();
                $('#assignedTicketsBadge').hide();
            }
        });
    }

    // ⏱️ Iniciar sistema
    setTimeout(function() {
        checkNotifications();
        const pollInterval = setInterval(checkNotifications, 15000);

        // Click en badges
        $('#newTicketsBadge, #assignedTicketsBadge').click(function(e) {
            e.preventDefault();
            e.stopPropagation();
            if (isAdmin) {
                window.location.href = '{{ route("admin.admin_solicitudes") }}?status=nuevo';
            } else if (isSupport) {
                window.location.href = '{{ route("admin.admin_solicitudes") }}?status=atendiendo';
            }
        });

        // Limpiar al salir
        $(window).on('beforeunload', function() {
            clearInterval(pollInterval);
            restoreTitle();
        });

    }, 2000);
});
</script>

    @yield('scripts')
</body>
</html>
