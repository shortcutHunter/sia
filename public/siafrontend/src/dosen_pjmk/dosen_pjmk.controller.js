(function() {
  'use strict';

  angular
    .module('app.dosen_pjmk')
    .controller('DosenPjmkController', DosenPjmkController);

  DosenPjmkController.$inject = ['$q', 'dataservice', 'logger', '$scope'];
  /* @ngInject */
  function DosenPjmkController($q, dataservice, logger, scope) {
    var vm = this;
    vm.title = 'Dosen PJMK';
    vm.table = 'dosen_pjmk';
    vm.page = 0;
    vm.data = [];
    vm.pageData = {};

    activate();

    scope.$watch('vm.page', function(newVal){
      if (newVal != 0) {
        getData();
      }
    });

    function activate() {
      vm.page = 1;
    }

    function getData() {
      return dataservice.getData(vm.table, vm.page).then(function(response) {
        vm.pageData = response;
        vm.data = response.data;
        logger.info('Data loaded');
      });
    }

  }
})();
