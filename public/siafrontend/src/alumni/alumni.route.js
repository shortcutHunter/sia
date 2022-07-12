(function() {
  'use strict';

  angular
    .module('app.alumni')
    .run(appRun);

  appRun.$inject = ['routerHelper'];
  /* @ngInject */
  function appRun(routerHelper) {
    routerHelper.configureStates(getStates());
  }

  function getStates() {
    return [
      {
        state: 'alumni',
        config: {
          url: '/alumni',
          templateUrl: '/template/alumni/table',
          controller: 'AlumniController',
          controllerAs: 'vm',
          title: 'alumni',
          settings: {
            nav: 1,
            content: '<i class="fa fa-list"></i> <span>Alumni</span>',
            parent: 'alumni',
            roles: ['admin']
          }
        }
      },
      {
        state: 'alumni_form',
        config: {
          url: '/alumni/form/:dataId',
          templateUrl: '/template/alumni/form',
          controller: 'AlumniFormController',
          controllerAs: 'vm',
          title: 'alumni'
        }
      },
      {
        state: 'alumni_detail',
        config: {
          url: '/alumni/{dataId}',
          templateUrl: '/template/alumni/detail',
          controller: 'AlumniDetailController',
          controllerAs: 'vm',
          title: 'alumni'
        }
      }
    ];
  }
})();
