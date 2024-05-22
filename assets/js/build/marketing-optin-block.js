!function(){"use strict";function e(e,t,o){return t in e?Object.defineProperty(e,t,{value:o,enumerable:!0,configurable:!0,writable:!0}):e[t]=o,e}var t=window.wp.blocks,o=window.wp.element,n=window.wp.blockEditor,r=window.wc.wcSettings,a=window.wp.components,i=function(e){let{icon:t,size:n=24,...r}=e;return(0,o.cloneElement)(t,{width:n,height:n,...r})},c=window.wp.primitives,l=(0,o.createElement)(c.SVG,{xmlns:"http://www.w3.org/2000/svg",viewBox:"0 0 24 24"},(0,o.createElement)(c.Path,{fillRule:"evenodd",d:"M6.863 13.644L5 13.25h-.5a.5.5 0 01-.5-.5v-3a.5.5 0 01.5-.5H5L18 6.5h2V16h-2l-3.854-.815.026.008a3.75 3.75 0 01-7.31-1.549zm1.477.313a2.251 2.251 0 004.356.921l-4.356-.921zm-2.84-3.28L18.157 8h.343v6.5h-.343L5.5 11.823v-1.146z",clipRule:"evenodd"})),u=window.wp.i18n,p=window.wc.blocksCheckout,s=(0,r.getSetting)("automatewoo_data").optinEnabled,m=(0,r.getSetting)("adminUrl"),w=function(){return(0,o.createElement)(a.Placeholder,{icon:(0,o.createElement)(i,{icon:l}),label:(0,u.__)("Marketing opt-in","automatewoo"),className:"wp-block-automatewoo-marketing-optin-placeholder"},(0,o.createElement)("span",{className:"wp-block-automatewoo-marketing-optin-placeholder__description"},(0,u.__)("AutomateWoo marketing opt-in would be shown here if enabled. You can enable this from the settings page.","automatewoo")),(0,o.createElement)(a.Button,{isPrimary:!0,href:"".concat(m,"admin.php?page=automatewoo-settings"),target:"_blank",rel:"noopener noreferrer",className:"wp-block-automatewoo-marketing-optin-placeholder__button"},(0,u.__)("Enable opt-in for Checkout","automatewoo")))},b=JSON.parse('{"apiVersion":2,"name":"automatewoo/marketing-optin","version":"0.1.0","title":"AutomateWoo Marketing opt-in","category":"woocommerce","textdomain":"automatewoo","supports":{"multiple":false},"attributes":{"lock":{"type":"object","default":{"remove":true}}},"parent":["woocommerce/checkout-contact-information-block"],"editorScript":"file:../build/marketing-optin-block.js","editorStyle":"file:../build/marketing-optin-block.css"}'),d={text:{type:"string",default:(0,r.getSetting)("automatewoo_data","").optinDefaultText}};function g(e,t){var o=Object.keys(e);if(Object.getOwnPropertySymbols){var n=Object.getOwnPropertySymbols(e);t&&(n=n.filter((function(t){return Object.getOwnPropertyDescriptor(e,t).enumerable}))),o.push.apply(o,n)}return o}function f(t){for(var o=1;o<arguments.length;o++){var n=null!=arguments[o]?arguments[o]:{};o%2?g(Object(n),!0).forEach((function(o){e(t,o,n[o])})):Object.getOwnPropertyDescriptors?Object.defineProperties(t,Object.getOwnPropertyDescriptors(n)):g(Object(n)).forEach((function(e){Object.defineProperty(t,e,Object.getOwnPropertyDescriptor(n,e))}))}return t}(0,t.registerBlockType)(b,{edit:function(e){var t=e.attributes.text,r=e.setAttributes,a=(0,n.useBlockProps)();return(0,o.createElement)("div",a,s?(0,o.createElement)(p.CheckboxControl,{id:"automatewoo-marketing-optin",checked:!1,disabled:!0},(0,o.createElement)(n.RichText,{className:"wc-block-components-checkbox__label",value:t,onChange:function(e){return r({text:e})}})):(0,o.createElement)(w,null))},save:function(){return(0,o.createElement)("div",n.useBlockProps.save())},attributes:f(f({},b.attributes),d)})}();