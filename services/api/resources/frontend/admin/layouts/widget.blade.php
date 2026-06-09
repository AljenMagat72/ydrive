<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >
    <title>Driver Schedule Widget</title>

    {{ Vite::useHotFile('hot/admin')->useBuildDirectory('build/admin')->withEntryPoints(['resources/frontend/admin/app.css'])}}
    @filamentStyles
    @livewireStyles
</head>

<body>
    <div class="w-dvw h-dvh grid grid-flow-col auto-cols-fr p-2">
        {{ $slot }}
    </div>
    @filamentScripts
    @livewireScripts
    @stack('scripts')
</body>

</html>
