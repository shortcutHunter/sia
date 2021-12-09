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
            parent: 'Kemahasiswaan',
            roles: ['admin', 'akademik']
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
      },

      {
        state: 'mahasiswa_krs',
        config: {
          url: '/mahasiswa/krs',
          templateUrl: '/template/mahasiswa/krs',
          controller: 'MahasiswaKrsController',
          controllerAs: 'vm',
          title: 'mahasiswa_krs',
          settings: {
            nav: 1,
            content: '<i class="fa fa-list"></i> <span>KRS</span>',
            parent: 'Kemahasiswaan',
            roles: ['mahasiswa']
          }
        }
      },
      {
        state: 'mahasiswa_khs',
        config: {
          url: '/mahasiswa/khs',
          templateUrl: '/template/mahasiswa/khs',
          controller: 'MahasiswaKhsController',
          controllerAs: 'vm',
          title: 'mahasiswa_khs',
          settings: {
            nav: 1,
            content: '<i class="fa fa-list"></i> <span>KHS</span>',
            parent: 'Kemahasiswaan',
            roles: ['mahasiswa']
          }
        }
      },
      {
        state: 'mahasiswa_tagihan',
        config: {
          url: '/mahasiswa/tagihan',
          templateUrl: '/template/mahasiswa/tagihan',
          controller: 'MahasiswaTagihanController',
          controllerAs: 'vm',
          title: 'mahasiswa_tagihan',
          settings: {
            nav: 1,
            content: '<i class="fa fa-list"></i> <span>Tagihan</span>',
            parent: 'Kemahasiswaan',
            roles: ['mahasiswa']
          }
        }
      },

    ];
  }
})();
