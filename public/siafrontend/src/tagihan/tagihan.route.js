(function() {
  'use strict';

  angular
    .module('app.tagihan')
    .run(appRun);

  appRun.$inject = ['routerHelper'];
  /* @ngInject */
  function appRun(routerHelper) {
    routerHelper.configureStates(getStates());
  }

  function getStates() {
    return [
      {
        state: 'tagihan',
        config: {
          url: '/tagihan',
          templateUrl: '/template/tagihan/table',
          controller: 'TagihanController',
          controllerAs: 'vm',
          title: 'tagihan',
          settings: {
            nav: 1,
            content: '<i class="fa fa-list"></i> <span>Tagihan</span>',
            parent: 'Keuangan',
            roles: ['admin', 'keuangan']
          }
        }
      },
      {
        state: 'tagihan_form',
        config: {
          url: '/tagihan/form/:dataId',
          templateUrl: '/template/tagihan/form',
          controller: 'TagihanFormController',
          controllerAs: 'vm',
          title: 'tagihan_form'
        }
      },
      {
        state: 'tagihan_detail',
        config: {
          url: '/tagihan/{dataId}',
          templateUrl: '/template/tagihan/detail',
          controller: 'TagihanDetailController',
          controllerAs: 'vm',
          title: 'tagihan_detail'
        }
      }
    ];
  }
})();
