(function() {
  'use strict';

  angular
    .module('app.sequance')
    .run(appRun);

  appRun.$inject = ['routerHelper'];
  /* @ngInject */
  function appRun(routerHelper) {
    routerHelper.configureStates(getStates());
  }

  function getStates() {
    return [
      {
        state: 'sequance',
        config: {
          url: '/sequance',
          templateUrl: '/template/sequance/table',
          controller: 'SequanceController',
          controllerAs: 'vm',
          title: 'sequance',
          settings: {
            nav: 1,
            content: '<i class="fa fa-list"></i> <span>Sequance</span>',
            parent: 'Konfigurasi',
            roles: ['admin']
          }
        }
      },
      {
        state: 'sequance_form',
        config: {
          url: '/sequance/form/:dataId',
          templateUrl: '/template/sequance/form',
          controller: 'SequanceFormController',
          controllerAs: 'vm',
          title: 'sequance_form'
        }
      },
      {
        state: 'sequance_detail',
        config: {
          url: '/sequance/{dataId}',
          templateUrl: '/template/sequance/detail',
          controller: 'SequanceDetailController',
          controllerAs: 'vm',
          title: 'sequance_detail'
        }
      }
    ];
  }
})();
