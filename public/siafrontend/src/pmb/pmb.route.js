(function() {
  'use strict';

  angular
    .module('app.pmb')
    .run(appRun);

  appRun.$inject = ['routerHelper'];
  /* @ngInject */
  function appRun(routerHelper) {
    routerHelper.configureStates(getStates());
  }

  function getStates() {
    return [
      {
        state: 'pmb',
        config: {
          url: '/pmb',
          templateUrl: '/template/pmb/table',
          controller: 'PmbController',
          controllerAs: 'vm',
          title: 'pmb',
          settings: {
            nav: 1,
            content: '<i class="fa fa-users"></i> <span>PMB</span>',
            parent: 'Kemahasiswaan',
            roles: ['admin', 'akademik', 'panitia']
          }
        }
      },
      {
        state: 'pmb_form',
        config: {
          url: '/pmb/form/:dataId',
          templateUrl: '/template/pmb/form',
          controller: 'PmbFormController',
          controllerAs: 'vm',
          title: 'pmb'
        }
      },
      {
        state: 'pmb_detail',
        config: {
          url: '/pmb/{dataId}',
          templateUrl: '/template/pmb/detail',
          controller: 'PmbDetailController',
          controllerAs: 'vm',
          title: 'pmb'
        }
      }
    ];
  }
})();
