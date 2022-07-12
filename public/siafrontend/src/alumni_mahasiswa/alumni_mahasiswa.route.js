(function() {
  'use strict';

  angular
    .module('app.alumni_mahasiswa')
    .run(appRun);

  appRun.$inject = ['routerHelper'];
  /* @ngInject */
  function appRun(routerHelper) {
    routerHelper.configureStates(getStates());
  }

  function getStates() {
    return [
      {
        state: 'alumni_mahasiswa_form',
        config: {
          url: '/alumni_mahasiswa/form',
          templateUrl: '/template/alumni/form',
          controller: 'AlumniMahasiswaFormController',
          controllerAs: 'vm',
          title: 'alumni_mahasiswa'
        }
      },
      {
        state: 'alumni_mahasiswa_detail',
        config: {
          url: '/alumni_mahasiswa',
          templateUrl: '/template/alumni/detail',
          controller: 'AlumniMahasiswaDetailController',
          controllerAs: 'vm',
          title: 'alumni_mahasiswa',
          settings: {
            nav: 1,
            content: '<i class="fa fa-list"></i> <span>Alumni</span>',
            parent: 'alumni_mahasiswa',
            roles: ['alumni']
          }
        }
      }
    ];
  }
})();
