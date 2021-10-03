(function() {
  'use strict';

  angular
    .module('app.kwitansi')
    .controller('KwitansiController', KwitansiController);

  KwitansiController.$inject = ['$q', 'dataservice', 'logger', '$scope'];
  /* @ngInject */
  function KwitansiController($q, dataservice, logger, scope) {
    var vm = this;
    vm.title = 'Kwitansi';
    vm.table = 'kwitansi';
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
