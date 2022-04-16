(function() {
  'use strict';

  angular
    .module('app.tahun_ajaran')
    .controller('TahunAjaranDetailController', TahunAjaranDetailController);

  TahunAjaranDetailController.$inject = ['$q', 'dataservice', 'logger', '$stateParams'];
  /* @ngInject */
  function TahunAjaranDetailController($q, dataservice, logger, stateParams) {
    var vm = this;
    vm.title = 'Detail Tahun Ajaran';
    vm.table = 'tahun_ajaran';
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
