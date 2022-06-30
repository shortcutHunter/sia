(function() {
  'use strict';

  angular
    .module('app.profile')
    .run(appRun);

  appRun.$inject = ['routerHelper'];
  /* @ngInject */
  function appRun(routerHelper) {
    routerHelper.configureStates(getStates());
  }

  function getStates() {
    return [
      {
        state: 'profile_detail',
        config: {
          url: '/profile',
          templateUrl: '/template/profile/detail',
          controller: 'ProfileDetailController',
          controllerAs: 'vm',
          title: 'profile',
          settings: {
            nav: 1,
            content: '<i class="fa fa-users"></i> <span>Profile</span>',
            parent: 'Profile',
            roles: ['admin', 'akademik', 'panitia', 'mahasiswa', 'dosen', 'keuangan', 'pegawai']
          }
        }
      },
      {
        state: 'profile_form',
        config: {
          url: '/profile/form',
          templateUrl: '/template/profile/form',
          controller: 'ProfileFormController',
          controllerAs: 'vm',
          title: 'profile'
        }
      }      
    ];
  }
})();
