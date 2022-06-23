(function() {
  'use strict';

  angular
    .module('app.tracking')
    .run(appRun);

  appRun.$inject = ['routerHelper'];
  /* @ngInject */
  function appRun(routerHelper) {
    routerHelper.configureStates(getStates());
  }

  function getStates() {
    return [
      {
        state: 'tracking',
        config: {
          url: '/tracking',
          templateUrl: '/template/tracking/detail',
          controller: 'TrackingController',
          controllerAs: 'vm',
          title: 'tracking',
          settings: {
            nav: 1,
            content: '<i class="fa fa-list"></i> <span>Tracking</span>',
            parent: 'Kemahasiswaan',
            roles: ['pmb']
          }
        }
      },

    ];
  }
})();
