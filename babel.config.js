/*
 * @Author: Bin
 * @Date: 2021-05-07
 * @FilePath: /PaperAirplane/babel.config.js
 */

module.exports = {
    plugins: [
      ['@babel/plugin-proposal-decorators', { legacy: true }],
      ['@babel/plugin-proposal-class-properties', { loose: true }],
      '@babel/plugin-syntax-dynamic-import',
      '@babel/plugin-transform-regenerator',
      [
        '@babel/plugin-transform-runtime',
        {
          helpers: false,
          regenerator: true,
        },
      ],
    ],
  };
