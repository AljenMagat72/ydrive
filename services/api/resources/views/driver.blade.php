<html>

<head>
    <meta charset="utf-8" />
    <meta
        name="viewport"
        content="width=device-width, initial-scale=1"
    >
    {{ Vite::useHotFile('hot/driver-portal')->useBuildDirectory('build/driver-portal')->withEntryPoints(['resources/frontend/driver-portal/src/main.ts'])}}
    @inertiaHead
</head>

<body>
    @inertia
</body>

</html>
