import { __ } from '@wordpress/i18n';
import { speak } from '@wordpress/a11y';
import { SELECTORS } from './constants';
import API from './classes/api';
import ControlSync from './classes/control/control-sync';
import ControlStates from './classes/control/control-states';
import ControlMeta from './classes/control/control-meta';

class OptimizationControl {
	constructor() {
		this.init();
	}

	init() {
		this.initEventListeners();

		const controlSync = new ControlSync();
		setInterval( () => controlSync.run(), 5000 );
	}

	initEventListeners() {
		document.addEventListener( 'click', ( e ) => this.handleOptimizeButtonClick( e ) );
		document.addEventListener( 'click', ( e ) => this.handleReoptimizeButtonClick( e ) );
		document.addEventListener( 'click', ( e ) => this.handleRestoreButtonClick( e ) );
	}

	async handleOptimizeButtonClick( e ) {
		if ( ! e.target.closest( `${ SELECTORS.optimizeButtonSelector }, ${ SELECTORS.tryAgainOptimizeButtonSelector }` ) ) {
			return;
		}

		speak( __( 'Optimization is in progress', 'image-optimization' ), 'assertive' );

		const controlWrapper = e.target.closest( SELECTORS.controlWrapperSelector );
		const states = new ControlStates( controlWrapper );

		states.renderLoading( 'optimize' );

		try {
			await API.optimizeSingleImage( {
				imageId: new ControlMeta( controlWrapper ).getImageId(),
				reoptimize: false,
			} );
		} catch ( error ) {
			states.renderError( error );
		}
	}

	async handleReoptimizeButtonClick( e ) {
		if ( ! e.target.closest( `${ SELECTORS.reoptimizeButtonSelector }, ${ SELECTORS.tryAgainReoptimizeButtonSelector }` ) ) {
			return;
		}

		speak( __( 'Reoptimizing is in progress', 'image-optimization' ), 'assertive' );

		const controlWrapper = e.target.closest( SELECTORS.controlWrapperSelector );
		const states = new ControlStates( controlWrapper );

		states.renderLoading( 'reoptimize' );

		try {
			await API.optimizeSingleImage( {
				imageId: new ControlMeta( controlWrapper ).getImageId(),
				reoptimize: true,
			} );
		} catch ( error ) {
			states.renderError( error );
		}
	}

	async handleRestoreButtonClick( e ) {
		if ( ! e.target.closest( `${ SELECTORS.restoreButtonSelector }, ${ SELECTORS.tryAgainRestoreButtonSelector }` ) ) {
			return;
		}

		speak( __( 'Image restoring is in progress', 'image-optimization' ), 'assertive' );

		const controlWrapper = e.target.closest( SELECTORS.controlWrapperSelector );
		const states = new ControlStates( controlWrapper );

		states.renderLoading( 'restore' );

		try {
			await API.restoreSingleImage( new ControlMeta( controlWrapper ).getImageId() );
		} catch ( error ) {
			states.renderError( error );
		}
	}
}

export default OptimizationControl;
