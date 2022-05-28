(function() {
  'use strict';

  angular
    .module('app.tagihan')
    .controller('TagihanDetailController', TagihanDetailController);

  TagihanDetailController.$inject = ['$q', 'dataservice', 'logger', '$stateParams', '$scope', '$state', '$compile'];
  /* @ngInject */
  function TagihanDetailController($q, dataservice, logger, stateParams, scope, state, compile) {
    var vm = this;
    vm.title = 'Detail Tagihan';
    vm.table = 'tagihan';
    vm.data = {};
    vm.option = {};

    vm.tambahPembiayaan = tambahPembiayaan;
    vm.hapusBukti = hapusBukti;
    vm.verif = verif;

    activate();

    function activate() {
      var promises = [getDataDetail(), getOption()];
      return $q.all(promises).then(function() {
        
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

    function tambahPembiayaan() {
      let el = "<modal-bayar-tagihan data='vm.data'></modal-bayar-tagihan>";
      el = compile(el)(scope);
    }

    function hapusBukti(data) {
      dataservice.deleteRecord('tagihan_bukti_bayar', data.id).then(() => state.reload());
    }

    function verif(data) {
      dataservice.postData('transaksi', {'status': 'verified'}, data.id).then(() => state.reload());
    }

  }
})();
