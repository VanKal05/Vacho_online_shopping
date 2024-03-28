const ARModelsBlockFrontend = ({ attributes }) => {
  const { post_id } = attributes;

  return <div>{`Selected Post: ${post_id}`}</div>;
};

wp.blocks.registerBlockType('armodels/armodels-block', {
  title: 'AR Model',
  category: 'armodels',
  icon: 'image-flip-horizontal',
  edit: () => null,
  save: ARModelsBlockFrontend,
});
