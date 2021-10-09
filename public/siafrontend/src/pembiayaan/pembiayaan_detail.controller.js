(function() {
  'use strict';

  angular
    .module('app.pembiayaan')
    .controller('PembiayaanDetailController', PembiayaanDetailController);

  PembiayaanDetailController.$inject = ['$q', 'dataservice', 'logger', '$stateParams'];
  /* @ngInject */
  function PembiayaanDetailController($q, dataservice, logger, stateParams) {
    var vm = this;
    vm.title = 'Detail Pembiayaan';
    vm.table = 'item';
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
