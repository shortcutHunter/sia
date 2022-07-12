(function() {
  'use strict';

  angular
    .module('app.karyawan')
    .controller('KaryawanDetailController', KaryawanDetailController);

  KaryawanDetailController.$inject = ['$q', 'dataservice', 'logger', '$stateParams'];
  /* @ngInject */
  function KaryawanDetailController($q, dataservice, logger, stateParams) {
    var vm = this;
    vm.title = 'Detail Karyawan';
    vm.table = 'karyawan';
    vm.data = {};

    activate();

    function activate() {
      var promises = [getDataDetail(), getOption()];
      return $q.all(promises).then(function() {
        
      });
    }

    function getDataDetail() {
      return dataservice.getDataDetail(vm.table, stateParams.dataId).then(function(response) {
        vm.data = response.data;
        getUser();
      });
    }

    function getUser() {
      return dataservice.getUrl(`/get/user/detail/${vm.data.orang_id}`).then(function(response) {
        vm.data.orang.user = response.data;
      });
    }

    function getOption() {
      return dataservice.getOption(vm.table).then(function(response) {
        vm.option = response;
      });
    }
  }
})();
