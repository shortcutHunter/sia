(function() {
  'use strict';

  angular
    .module('app.paket')
    .controller('PaketController', PaketController);

  PaketController.$inject = ['$q', 'dataservice', 'logger', '$scope'];
  /* @ngInject */
  function PaketController($q, dataservice, logger, scope) {
    var vm = this;
    vm.title = 'Paket';
    vm.table = 'paket';
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
        'field': 'nama',
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
