(function() {
  'use strict';

  angular
    .module('app.karyawan')
    .run(appRun);

  appRun.$inject = ['routerHelper'];
  /* @ngInject */
  function appRun(routerHelper) {
    routerHelper.configureStates(getStates());
  }

  function getStates() {
    return [
      {
        state: 'karyawan',
        config: {
          url: '/karyawan',
          templateUrl: '/template/karyawan/table',
          controller: 'KaryawanController',
          controllerAs: 'vm',
          title: 'Kepegawaian',
          settings: {
            nav: 1,
            content: '<i class="fa fa-list"></i> <span>Kepegawaian</span>',
            parent: 'Karyawan',
            roles: ['admin', 'akademik']
          }
        }
      },
      {
        state: 'karyawan_form',
        config: {
          url: '/karyawan/form/:dataId',
          templateUrl: '/template/karyawan/form',
          controller: 'KaryawanFormController',
          controllerAs: 'vm',
          title: 'karyawan_form'
        }
      },
      {
        state: 'karyawan_detail',
        config: {
          url: '/karyawan/{dataId}',
          templateUrl: '/template/karyawan/detail',
          controller: 'KaryawanDetailController',
          controllerAs: 'vm',
          title: 'karyawan_detail'
        }
      }
    ];
  }
})();
