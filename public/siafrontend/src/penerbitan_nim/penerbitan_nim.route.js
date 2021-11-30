(function() {
  'use strict';

  angular
    .module('app.penerbitan_nim')
    .run(appRun);

  appRun.$inject = ['routerHelper'];
  /* @ngInject */
  function appRun(routerHelper) {
    routerHelper.configureStates(getStates());
  }

  function getStates() {
    return [
      {
        state: 'penerbitan_nim',
        config: {
          url: '/penerbitan_nim',
          templateUrl: '/template/penerbitan_nim/table',
          controller: 'PenerbitanNimController',
          controllerAs: 'vm',
          title: 'penerbitan_nim',
          settings: {
            nav: 1,
            content: '<i class="fa fa-list"></i> <span>Penerbitan NIM</span>',
            parent: 'Kemahasiswaan',
            roles: ['admin', 'akademik']
          }
        }
      }
    ];
  }
})();
