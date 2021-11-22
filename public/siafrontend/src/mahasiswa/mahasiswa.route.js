(function() {
  'use strict';

  angular
    .module('app.mahasiswa')
    .run(appRun);

  appRun.$inject = ['routerHelper'];
  /* @ngInject */
  function appRun(routerHelper) {
    routerHelper.configureStates(getStates());
  }

  function getStates() {
    return [
      {
        state: 'mahasiswa',
        config: {
          url: '/mahasiswa',
          templateUrl: '/template/mahasiswa/table',
          controller: 'MahasiswaController',
          controllerAs: 'vm',
          title: 'mahasiswa',
          settings: {
            nav: 1,
            content: '<i class="fa fa-list"></i> <span>Mahasiswa</span>',
            parent: 'Kemahasiswaan'
          }
        }
      },
      {
        state: 'mahasiswa_form',
        config: {
          url: '/mahasiswa/form/:dataId',
          templateUrl: '/template/mahasiswa/form',
          controller: 'MahasiswaFormController',
          controllerAs: 'vm',
          title: 'mahasiswa_form'
        }
      },
      {
        state: 'mahasiswa_detail',
        config: {
          url: '/mahasiswa/{dataId}',
          templateUrl: '/template/mahasiswa/detail',
          controller: 'MahasiswaDetailController',
          controllerAs: 'vm',
          title: 'mahasiswa_detail'
        }
      },
      {
        state: 'mahasiswa_riwayat_belajar',
        config: {
          url: '/mahasiswa/riwayat/{dataId}',
          templateUrl: '/template/mahasiswa/riwayat_belajar',
          controller: 'MahasiswaRiwayatBelajarController',
          controllerAs: 'vm',
          title: 'mahasiswa_riwayat_belajar'
        }
      }
    ];
  }
})();
