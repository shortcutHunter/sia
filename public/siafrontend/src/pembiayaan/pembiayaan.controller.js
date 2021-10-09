(function() {
  'use strict';

  angular
    .module('app.pembiayaan')
    .controller('PembiayaanController', PembiayaanController);

  PembiayaanController.$inject = ['$q', 'dataservice', 'logger', '$scope'];
  /* @ngInject */
  function PembiayaanController($q, dataservice, logger, scope) {
    var vm = this;
    vm.title = 'Pembiayaan';
    vm.table = 'item';
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
