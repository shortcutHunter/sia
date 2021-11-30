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
    vm.searchValue = false;

    vm.search = search;

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
      return dataservice.getData(vm.table, vm.page, vm.searchValue).then(function(response) {
        vm.pageData = response;
        vm.data = response.data;
        logger.info('Data loaded');
      });
    }

    function search() {
      vm.searchValue = {
        'field': 'karyawan',
        'value': angular.copy(vm.searchData)
      };
      if (vm.page == 1) {
        getData();
      }else{
        vm.page = 1;
      }
    }

  }
})();
