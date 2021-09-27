(function() {
  'use strict';

  angular
    .module('app.nilai')
    .controller('NilaiDetailController', NilaiDetailController);

  NilaiDetailController.$inject = ['$q', 'dataservice', 'logger', '$stateParams'];
  /* @ngInject */
  function NilaiDetailController($q, dataservice, logger, stateParams) {
    var vm = this;
    vm.title = 'Detail Nilai';
    vm.table = 'nilai';
    vm.data = {};

    activate();

    function activate() {
      var promises = [getDataDetail()];
      return $q.all(promises).then(function() {
        logger.info('Data loaded');
      });
    }

    function getDataDetail() {
      return dataservice.getDataDetail(vm.table, stateParams.dataId).then(function(response) {
        vm.data = response.data;
      });
    }
  }
})();
