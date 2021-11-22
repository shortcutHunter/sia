(function() {
  'use strict';

  angular
    .module('app.agama')
    .run(appRun);

  appRun.$inject = ['routerHelper'];
  /* @ngInject */
  function appRun(routerHelper) {
    routerHelper.configureStates(getStates());
  }

  function getStates() {
    return [
      {
        state: 'agama',
        config: {
          url: '/agama',
          templateUrl: '/template/agama/table',
          controller: 'AgamaController',
          controllerAs: 'vm',
          title: 'agama',
          settings: {
            nav: 1,
            content: '<i class="fa fa-list"></i> <span>Agama</span>',
            parent: 'Konfigurasi'
          }
        }
      },
      {
        state: 'agama_form',
        config: {
          url: '/agama/form/:dataId',
          templateUrl: '/template/agama/form',
          controller: 'AgamaFormController',
          controllerAs: 'vm',
          title: 'agama'
        }
      },
      {
        state: 'agama_detail',
        config: {
          url: '/agama/{dataId}',
          templateUrl: '/template/agama/detail',
          controller: 'AgamaDetailController',
          controllerAs: 'vm',
          title: 'agama'
        }
      }
    ];
  }
})();
