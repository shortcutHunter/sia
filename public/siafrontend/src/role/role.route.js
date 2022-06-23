(function() {
  'use strict';

  angular
    .module('app.role')
    .run(appRun);

  appRun.$inject = ['routerHelper'];
  /* @ngInject */
  function appRun(routerHelper) {
    routerHelper.configureStates(getStates());
  }

  function getStates() {
    return [
      {
        state: 'role',
        config: {
          url: '/role',
          templateUrl: '/template/role/table',
          controller: 'RoleController',
          controllerAs: 'vm',
          title: 'role',
          settings: {
            nav: 1,
            content: '<i class="fa fa-list"></i> <span>Role</span>',
            parent: 'Konfigurasi',
            roles: ['admin']
          }
        }
      },
      {
        state: 'role_form',
        config: {
          url: '/role/form/:dataId',
          templateUrl: '/template/role/form',
          controller: 'RoleFormController',
          controllerAs: 'vm',
          title: 'role'
        }
      },
      {
        state: 'role_detail',
        config: {
          url: '/role/{dataId}',
          templateUrl: '/template/role/detail',
          controller: 'RoleDetailController',
          controllerAs: 'vm',
          title: 'role'
        }
      }
    ];
  }
})();
