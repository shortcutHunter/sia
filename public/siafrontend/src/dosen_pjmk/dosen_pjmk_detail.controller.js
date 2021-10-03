(function() {
  'use strict';

  angular
    .module('app.dosen_pjmk')
    .controller('DosenPjmkDetailController', DosenPjmkDetailController);

  DosenPjmkDetailController.$inject = ['$q', 'dataservice', 'logger', '$stateParams', '$compile', '$scope', '$element'];
  /* @ngInject */
  function DosenPjmkDetailController($q, dataservice, logger, stateParams, compile, scope, element) {
    var vm = this;
    vm.title = 'Detail Dosen PJMK';
    vm.table = 'dosen_pjmk';
    vm.data = {};

    vm.tambahMatakuliah = tambahMatakuliah;
    vm.konfigureNilai = konfigureNilai;
    vm.isiNilai = isiNilai;

    activate();

    function activate() {
      var promises = [getDataDetail(), getOption()];
      return $q.all(promises).then(function() {
        logger.info('Data loaded');
      });
    }

    function getDataDetail() {
      return dataservice.getDataDetail(vm.table, stateParams.dataId).then(function(response) {
        vm.data = response.data;
      });
    }

    function getOption() {
      return dataservice.getOption(vm.table).then(function(response) {
        vm.option = response;
      });
    }

    function tambahMatakuliah() {
      let el = "<modal-mata-kuliah data='data.mata_kuliah'></modal-mata-kuliah>";
      el = compile(el)(scope);
      $(element).append(el);
    }

    function konfigureNilai(data) {
      vm.active_data_nilai = data;
      let el = "<modal-konfigurasi-nilai data='vm.active_data_nilai'></modal-konfigurasi-nilai>";
      el = compile(el)(scope);
    }

    function isiNilai(data) {
      vm.active_data = data;
      let el = "<modal-isi-nilai data='vm.active_data'></modal-isi-nilai>";
      el = compile(el)(scope);
    }
  }
})();
