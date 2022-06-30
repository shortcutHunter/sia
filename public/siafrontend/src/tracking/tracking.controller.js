(function() {
  'use strict';

  angular
    .module('app.tracking')
    .controller('TrackingController', TrackingController);

  TrackingController.$inject = ['$q', 'dataservice', 'logger', '$stateParams', '$compile', '$element', '$scope'];
  /* @ngInject */
  function TrackingController($q, dataservice, logger, stateParams, compile, element, scope) {
    var vm = this;
    vm.title = 'Tracking';
    vm.data = {};

    activate();

    function activate() {
      var promises = [getDataDetail()];
      return $q.all(promises).then(function() {
        getPendaftaran();
      });
    }

    scope.$watch('vm.data.dokumen_kesehatan', uploadDocument);

    function getDataDetail() {
      return dataservice.getUrl('/pmb/baru').then(function(response) {
        vm.data = response.data;
      });
    }

    function getPendaftaran() {
      return dataservice.getUrl(`/pendaftaran/get/${vm.data.pendaftaran_id}`).then(function(response) {
        vm.pendaftaran = response.data;
      });
    }

    function uploadDocument(newVal, oldVal) {
      if (newVal && oldVal !== undefined) {
        return dataservice.postData('pmb', {dokumen_kesehatan: newVal}, vm.data.id).then(function(data){
          state.reload();
        });
      }
    }

  }
})();
