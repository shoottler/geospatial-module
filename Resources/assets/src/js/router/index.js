function loadView (view, path) {
  return () => import(/* webpackChunkName: "view-[request]" */ `../views/${path}/${view}.vue`)
}
export const routes = [
  {
    path     : 'service-areas',
    name     : 'Service areas',
    redirect : '/app/settings/service-areas/list',
    component: loadView('ServiceAreas', 'service-areas'),
    children : [
      {
        path     : 'list',
        name     : 'service areas list',
        component: loadView('ServiceAreasList', 'service-areas'),
      },
      {
        path     : 'create',
        name     : 'Create a service area',
        component: loadView('CreateServiceArea', 'service-areas'),
      },
      {
        path     : 'edit/:id',
        name     : 'Edit Service Area',
        component: loadView('EditServiceArea', 'service-areas'),
        props    : true,
      },
    ],
  },
  {
    path     : 'zones',
    name     : 'Zones',
    redirect : '/app/settings/zones/list',
    component: loadView('Zones', 'zones'),
    children : [
      {
        path     : 'list',
        name     : 'zones list',
        component: loadView('ZonesList', 'zones'),
      },
      {
        path     : 'create',
        name     : 'Create a zone',
        component: loadView('CreateZone', 'zones'),
      },
      {
        path     : 'edit/:id',
        name     : 'Edit Zone',
        component: loadView('EditZone', 'zones'),
        props    : true,
      },
    ],
  },
]
