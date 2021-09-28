(function() {
  'use strict';

  angular
    .module('app.tahun_ajaran')
    .controller('TahunAjaranController', TahunAjaranController);

  TahunAjaranController.$inject = ['$q', 'dataservice', 'logger', '$scope'];
  /* @ngInject */
  function TahunAjaranController($q, dataservice, logger, scope) {
    var vm = this;
    vm.title = 'Tahun Ajaran';
    vm.table = 'tahun_ajaran';
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