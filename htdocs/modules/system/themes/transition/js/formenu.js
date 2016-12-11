        startList = function() {
            if (document.all&&document.getElementById) {
                navRoot = document.getElementById("nav");
                if (navRoot)
                for (i=0; i<navRoot.childNodes.length; i++) {
                    node = navRoot.childNodes[i];
                    if (node.nodeName=="li") {
                        node.onmouseover=function() {
                            this.className+=" over";
                        }
                        node.onmouseout=function() {
                            this.className=this.className.replace(" over", "");
                        }
                    }
                }
            }
        }
        xoopsOnloadEvent(startList);