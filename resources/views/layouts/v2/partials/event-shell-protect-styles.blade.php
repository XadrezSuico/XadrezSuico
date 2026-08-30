<style>
    /* Protege sidebar, topbar e cabeçalho v2 contra resets globais do Bootstrap 3 */
    #v2-sidebar,
    #v2-sidebar *,
    #v2-app > div > header,
    #v2-app > div > header *,
    .v2-event-shell,
    .v2-event-shell * {
        box-sizing: border-box;
    }

    #v2-sidebar a,
    #v2-app > div > header a,
    .v2-event-shell a {
        text-decoration: none;
    }

    .v2-event-shell h1,
    .v2-event-shell h2 {
        margin-top: 0;
        margin-bottom: 0;
    }

    .v2-event-shell a.inline-flex,
    .v2-event-shell a[class*="rounded-"] {
        background-image: none;
        box-shadow: none;
        text-shadow: none;
    }
</style>
