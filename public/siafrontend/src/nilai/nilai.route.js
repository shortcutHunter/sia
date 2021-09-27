(function() {
  'use strict';

  angular
    .module('app.nilai')
    .run(appRun);

  appRun.$inject = ['routerHelper'];
  /* @ngInject */
  function appRun(routerHelper) {
    routerHelper.configureStates(getStates());
  }

  function getStates() {
    return [
      {
        state: 'nilai',
        config: {
          url: '/nilai',
          templateUrl: '/template/nilai/table',
          controller: 'NilaiController',
          controllerAs: 'vm',
          title: 'nilai',
          settings: {
            nav: 1,
            content: '<i class="fa fa-list"></i> <span>Nilai</span>'
          }
        }
      },
      {
        state: 'nilai_form',
        config: {
          url: '/nilai/form/:dataId',
          templateUrl: '/template/nilai/form',
          controller: 'NilaiFormController',
          controllerAs: 'vm',
          title: 'nilai'
        }
      },
      {
        state: 'nilai_detail',
        config: {
          url: '/nilai/{dataId}',
          templateUrl: '/template/nilai/detail',
          controller: 'NilaiDetailController',
          controllerAs: 'vm',
          title: 'nilai'
        }
      }
    ];
  }
})();
