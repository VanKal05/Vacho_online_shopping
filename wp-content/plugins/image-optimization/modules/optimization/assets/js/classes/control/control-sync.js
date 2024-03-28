import API from '../api';
import { SELECTORS } from '../../constants';
import ControlMeta from './control-meta';
import ControlStates from './control-states';

class ControlSync {
	async run() {
		const controls = document.querySelectorAll( SELECTORS.controlWrapperSelector );

		if ( ! controls.length ) {
			return;
		}

		const imageIds = this.mapImageIds( controls );
		const { status } = await API.getOptimizationStatus( imageIds );

		controls.forEach( ( controlWrapper ) => {
			const imageId = new ControlMeta( controlWrapper ).getImageId();
			const currentStatus = new ControlMeta( controlWrapper ).getStatus();
			const imageData = status[ imageId ];
			const controlStates = new ControlStates( controlWrapper );

			if ( currentStatus === imageData.status ) {
				return;
			}

			if ( currentStatus === 'error' && ! new ControlMeta( controlWrapper ).allowRetry() ) {
				return;
			}

			switch ( imageData.status ) {
				case 'optimization-in-progress':
					controlStates.renderLoading( 'optimize' );

					break;

				case 'reoptimizing-in-progress':
					controlStates.renderLoading( 'reoptimize' );

					break;

				case 'restoring-in-progress':
					controlStates.renderLoading( 'restore' );

					break;

				case 'not-optimized':
					controlStates.renderNotOptimized();

					break;

				case 'optimized':
					const statsData = {
						sizesOptimized: imageData.stats.optimized_image_count,
						saved: {
							absolute: imageData.stats.initial_image_size - imageData.stats.current_image_size,
							relative: Math.round( imageData.stats.current_image_size / imageData.stats.initial_image_size * 100 ),
						},
					};

					controlStates.renderOptimized( statsData );

					break;

				case 'optimization-failed':
					controlStates.renderError( {
						message: imageData.message,
						imagesLeft: imageData.images_left,
						action: 'optimize',
					} );

					break;

				case 'reoptimizing-failed':
					controlStates.renderError( {
						message: imageData.message,
						imagesLeft: imageData.images_left,
						action: 'reoptimize',
					} );

					break;

				case 'restoring-failed':
					controlStates.renderError( {
						message: imageData.message,
						action: 'restore',
					} );
			}
		} );
	}

	mapImageIds( nodes ) {
		return Array.prototype.map.call(
			nodes,
			( node ) => new ControlMeta( node ).getImageId(),
		);
	}
}

export default ControlSync;
