(function() {
  'use strict';

  angular
    .module('app.mata_kuliah')
    .controller('MataKuliahFormController', MataKuliahFormController);

  MataKuliahFormController.$inject = ['$q', 'dataservice', 'logger', '$stateParams'];
  /* @ngInject */
  function MataKuliahFormController($q, dataservice, logger, stateParams) {
    var vm = this;
    vm.title = 'Form Mata Kuliah';
    vm.table = 'mata_kuliah';

    activate();

    function activate() {
      if (stateParams.dataId) {
        var promises = [getDataDetail()];
        return $q.all(promises).then(function() {
          
        });
      }
    }

    function getDataDetail() {
      return dataservice.getDataDetail(vm.table, stateParams.dataId).then(function(response) {
        vm.data = response.data;
      });
    }
  }
})();
