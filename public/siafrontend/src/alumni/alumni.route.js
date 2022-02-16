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
          templateUrl: '/template/mahasiswa/table',
          controller: 'AlumniController',
          controllerAs: 'vm',
          title: 'alumni',
          settings: {
            nav: 1,
            content: '<i class="fa fa-list"></i> <span>Alumni</span>',
            parent: 'Kemahasiswaan',
            roles: ['admin', 'akademik']
          }
        }
      }
    ];
  }
})();
