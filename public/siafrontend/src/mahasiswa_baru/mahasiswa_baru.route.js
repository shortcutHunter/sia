(function() {
  'use strict';

  angular
    .module('app.mahasiswa_baru')
    .run(appRun);

  appRun.$inject = ['routerHelper'];
  /* @ngInject */
  function appRun(routerHelper) {
    routerHelper.configureStates(getStates());
  }

  function getStates() {
    return [
      {
        state: 'mahasiswa_baru',
        config: {
          url: '/mahasiswa_baru',
          templateUrl: '/template/mahasiswa_baru/detail',
          controller: 'MahasiswaBaruDetail',
          controllerAs: 'vm',
          title: 'pendaftaran_mahasiswa',
          settings: {
            nav: 1,
            content: '<i class="fa fa-list"></i> <span>Profile</span>',
            parent: 'Kemahasiswaan',
            roles: ['pmb']
          }
        }
      },

    ];
  }
})();
