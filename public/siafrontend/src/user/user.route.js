(function() {
  'use strict';

  angular
    .module('app.user')
    .run(appRun);

  appRun.$inject = ['routerHelper'];
  /* @ngInject */
  function appRun(routerHelper) {
    routerHelper.configureStates(getStates());
  }

  function getStates() {
    return [
      {
        state: 'user',
        config: {
          url: '/user',
          templateUrl: '/template/user/table',
          controller: 'UserController',
          controllerAs: 'vm',
          title: 'user',
          settings: {
            nav: 1,
            content: '<i class="fa fa-list"></i> <span>User</span>',
            parent: 'Konfigurasi',
            roles: ['admin']
          }
        }
      },
      {
        state: 'user_detail',
        config: {
          url: '/user/{dataId}',
          templateUrl: '/template/user/detail',
          controller: 'UserDetailController',
          controllerAs: 'vm',
          title: 'user'
        }
      }
    ];
  }
})();
