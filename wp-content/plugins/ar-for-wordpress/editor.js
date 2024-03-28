/*const { SelectControl } = wp.components;
const { withSelect } = wp.data;
const { createBlock } = wp.blocks;

const ARModelsBlock = ({ posts, setAttributes }) => {
  const options = posts.map(({ id, title }) => ({ value: id, label: title }));

  return (
    <SelectControl
      label="Select AR Model Post"
      value={attributes.post_id}
      options={options}
      onChange={(post_id) => setAttributes({ post_id })}
    />
  );
};

const ARModelsWithPosts = withSelect((select) => {
  const query = {
    per_page: -1,
    type: 'armodels',
  };

  return {
    posts: select('core').getEntityRecords('postType', 'armodels', query),
  };
})(ARModelsBlock);

wp.blocks.registerBlockType('armodels/armodels-block', {
  title: 'AR Model',
  category: 'common',
  icon: 'image-flip-horizontal',
  edit: ARModelsWithPosts,
  save: () => null,
});
*/


const { useSelect } = wp.data;
const pagesPosts = useSelect( ( select ) => {
  return select( 'core' ).getEntityRecords( 'postType', 'page', { status : 'publish' } );
} )

const { pages } = useSelect( ( select ) => {
  const { getEntityRecords } = select( 'core' );

  // Query args
  const query = {
    status: 'publish',
    per_page: 2
  }

  return {
    pages: getEntityRecords( 'postType', 'page', query ),
  }
} )

// populate options for <SelectControl>
let options = [];
if( pages ) {
  options.push( { value: 0, label: 'Select a page' } )
  pages.forEach( ( page ) => {
    options.push( { value : page.id, label : page.title.rendered } )
  })
} else {
  options.push( { value: 0, label: 'Loading...' } )
}

// display select dropdown
return (
  <PluginDocumentSettingPanel
    name="custom-panel"
    title="Misha's custom panel"
    className="some-css-class"
  >
    <SelectControl label="Select a post" options={ options } />
  </PluginDocumentSettingPanel>
)