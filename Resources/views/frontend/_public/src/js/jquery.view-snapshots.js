;(function($, window) {
    'use strict';

    if (window.snapshots.config.isRecordingSnapshots && !window.snapshots.config.currentStep) {
        console.log('📹 This session is currently being recorded. Session ID: ' + window.snapshots.config.sessionId);
    }

    if (window.snapshots.config.currentStep) {
        console.log('📹 Watching step ' + window.snapshots.config.currentStep + ' of recorded session ' + window.snapshots.config.sessionId);
    }

    window.snapshots.record = function() {
        var me = this;

        if (me.config.isRecordingSnapshots || !me.config.startUrl) {
            console.log('⚠️ Already recording or missing config.');

            return;
        }

        $.ajax({
            'method': 'GET',
            'url': me.config.startUrl,
            'success': function () {
                me.config.isRecordingSnapshots = true;

                console.log('▶️️ Recording of session starting next request. Session ID: ' + me.config.sessionId);
            },
            'error': function () {
                console.log('⚠️ Error while starting the recording of session.');
            }
        });
    };

    window.snapshots.stop = function() {
        var me = this;

        if (!me.config.isRecordingSnapshots || !me.config.stopUrl) {
            console.log('⚠️ Not recording at the moment.');

            return;
        }

        $.ajax({
            'method': 'GET',
            'url': me.config.stopUrl,
            'success': function () {
                me.config.isRecordingSnapshots = false;

                console.log('✋️️ Stopped recording current session.');
            },
            'error': function () {
                console.log('⚠️ Error while stopping the recording of session.');
            }
        });
    };

    window.snapshots.next = function() {
        var me = this;

        if (!me.config.nextUrl) {
            console.log('⚠️ No next snapshot recorded or currently not watching session.');

            return;
        }

        console.log('⬅️️ Loading next snapshot.');

        window.location.href = me.config.nextUrl;
    };

    window.snapshots.prev = function() {
        var me = this;

        if (!me.config.prevUrl) {
            console.log('⚠️ No previous snapshot recorded or currently not watching session.');

            return;
        }

        console.log('➡️️ Loading previous snapshot.');

        window.location.href = me.config.prevUrl;
    };

})(jQuery, window);