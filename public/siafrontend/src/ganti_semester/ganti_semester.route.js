(function() {
  'use strict';

  angular
    .module('app.ganti_semester')
    .run(appRun);

  appRun.$inject = ['routerHelper'];
  /* @ngInject */
  function appRun(routerHelper) {
    routerHelper.configureStates(getStates());
  }

  function getStates() {
    return [
      {
        state: 'ganti_semester',
        config: {
          url: '/ganti_semester',
          templateUrl: '/template/ganti_semester/table',
          controller: 'GantiSemesterController',
          controllerAs: 'vm',
          title: 'ganti_semester',
          settings: {
            nav: 1,
            content: '<i class="fa fa-list"></i> <span>Ganti Semester</span>',
            parent: 'Kemahasiswaan',
            roles: ['admin', 'akademik']
          }
        }
      },
      {
        state: 'ganti_semester_mahasiswa',
        config: {
          url: '/ganti_semester/{semester_id}',
          templateUrl: '/template/ganti_semester/mahasiswa',
          controller: 'GantiSemesterMahasiswaController',
          controllerAs: 'vm',
          title: 'ganti_semester_mahasiswa'
        }
      }
    ];
  }
})();
