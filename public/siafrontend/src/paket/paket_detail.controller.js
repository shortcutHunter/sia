(function() {
  'use strict';

  angular
    .module('app.paket')
    .controller('PaketDetailController', PaketDetailController);

  PaketDetailController.$inject = ['$q', 'dataservice', 'logger', '$stateParams'];
  /* @ngInject */
  function PaketDetailController($q, dataservice, logger, stateParams) {
    var vm = this;
    vm.title = 'Detail Paket';
    vm.table = 'paket';
    vm.data = {};

    activate();

    function activate() {
      var promises = [getDataDetail()];
      return $q.all(promises).then(function() {
        
      });
    }

    function getDataDetail() {
      return dataservice.getDataDetail(vm.table, stateParams.dataId).then(function(response) {
        vm.data = response.data;
      });
    }
  }
})();
