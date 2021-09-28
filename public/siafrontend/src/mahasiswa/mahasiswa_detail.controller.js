(function() {
  'use strict';

  angular
    .module('app.mahasiswa')
    .controller('MahasiswaDetailController', MahasiswaDetailController);

  MahasiswaDetailController.$inject = ['$q', 'dataservice', 'logger', '$stateParams'];
  /* @ngInject */
  function MahasiswaDetailController($q, dataservice, logger, stateParams) {
    var vm = this;
    vm.title = 'Detail Mahasiswa';
    vm.table = 'mahasiswa';
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
