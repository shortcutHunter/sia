(function() {
  'use strict';

  angular
    .module('app.mata_kuliah')
    .run(appRun);

  appRun.$inject = ['routerHelper'];
  /* @ngInject */
  function appRun(routerHelper) {
    routerHelper.configureStates(getStates());
  }

  function getStates() {
    return [
      {
        state: 'mata_kuliah',
        config: {
          url: '/mata_kuliah',
          templateUrl: '/template/mata_kuliah/table',
          controller: 'MataKuliahController',
          controllerAs: 'vm',
          title: 'mata_kuliah',
          settings: {
            nav: 1,
            content: '<i class="fa fa-list"></i> <span>Mata Kuliah</span>'
          }
        }
      },
      {
        state: 'mata_kuliah_form',
        config: {
          url: '/mata_kuliah/form/:dataId',
          templateUrl: '/template/mata_kuliah/form',
          controller: 'MataKuliahFormController',
          controllerAs: 'vm',
          title: 'mata_kuliah_form'
        }
      },
      {
        state: 'mata_kuliah_detail',
        config: {
          url: '/mata_kuliah/{dataId}',
          templateUrl: '/template/mata_kuliah/detail',
          controller: 'MataKuliahDetailController',
          controllerAs: 'vm',
          title: 'mata_kuliah_detail'
        }
      }
    ];
  }
})();
