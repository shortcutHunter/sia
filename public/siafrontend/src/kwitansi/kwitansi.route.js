(function() {
  'use strict';

  angular
    .module('app.kwitansi')
    .run(appRun);

  appRun.$inject = ['routerHelper'];
  /* @ngInject */
  function appRun(routerHelper) {
    routerHelper.configureStates(getStates());
  }

  function getStates() {
    return [
      {
        state: 'kwitansi',
        config: {
          url: '/kwitansi',
          templateUrl: '/template/kwitansi/table',
          controller: 'KwitansiController',
          controllerAs: 'vm',
          title: 'kwitansi',
          settings: {
            nav: 1,
            content: '<i class="fa fa-list"></i> <span>Kwitansi</span>'
          }
        }
      },
      {
        state: 'kwitansi_form',
        config: {
          url: '/kwitansi/form/:dataId',
          templateUrl: '/template/kwitansi/form',
          controller: 'KwitansiFormController',
          controllerAs: 'vm',
          title: 'kwitansi_form'
        }
      },
      {
        state: 'kwitansi_detail',
        config: {
          url: '/kwitansi/{dataId}',
          templateUrl: '/template/kwitansi/detail',
          controller: 'KwitansiDetailController',
          controllerAs: 'vm',
          title: 'kwitansi_detail'
        }
      }
    ];
  }
})();
