(function() {
  'use strict';

  angular
    .module('app.mata_kuliah')
    .controller('MataKuliahDetailController', MataKuliahDetailController);

  MataKuliahDetailController.$inject = ['$q', 'dataservice', 'logger', '$stateParams'];
  /* @ngInject */
  function MataKuliahDetailController($q, dataservice, logger, stateParams) {
    var vm = this;
    vm.title = 'Detail Mata Kuliah';
    vm.table = 'mata_kuliah';
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
