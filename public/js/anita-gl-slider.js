/**
 * Author: Shadow Themes
 * Author URL: https://shadow-themes.com
 */
"use strict";

class Anita_GL_Slider {
    constructor(b, c = !1) {
        if (b instanceof jQuery ? this.$el = b : this.$el = jQuery(b), !this.$el.length) return console.warn("gl_Gallery: Element not found"), !1;
        if (!this.$el.children("div").length) return console.warn("gl_Gallery: There are no slides"), !1;
        if (this.options = {
            container: jQuery("body"),
            perspective: 800,
            zoomed: 700,
            fit: "cover",
            type: "sliced",
            intensity: .5,
            bg_color: "#000000",
            nav: !0,
            wheelSensitive: 50
        }, c) for (let [d, e] of Object.entries(c)) this.options[d] = e;
        let a = this;
        this.loader = new THREE.TextureLoader, this.clock = new THREE.Clock, this.prevTime = 0, this.$el.css("transform", "translateX(0px)"), this.screen_ratio = window.innerHeight / window.innerWidth, this.isChanging = 0, this.cameraFixed = 0, this.scene = new THREE.Scene, this.fov = 180 * (2 * Math.atan(window.innerHeight / 2 / this.options.perspective)) / Math.PI, this.camera = new THREE.PerspectiveCamera(this.fov, window.innerWidth / window.innerHeight, .5, 1e3), this.camera.position.set(0, 0, this.options.zoomed), this.renderer = new THREE.WebGLRenderer({
            antialias: a.options.antialised, alpha: !1
        }), this.renderer.setSize(window.innerWidth, window.innerHeight), this.options.container[0].append(this.renderer.domElement), jQuery(this.renderer.domElement).addClass("anita-gl-slider-canvas"), this.gallery = new Array, this.$el.children("div").each(function (f) {
            let c = jQuery(this), d = c.attr("data-size").split("x"), b = {};
            if (b.ratio = d[1] / d[0], "cover" == a.options.fit ? b.ratio > a.screen_ratio ? b.zoom = new THREE.Vector2(1, a.screen_ratio / b.ratio) : this.ratio < a.screen_ratio ? b.zoom = new THREE.Vector2(b.ratio / a.screen_ratio, 1) : b.zoom = new THREE.Vector2(1, 1) : b.ratio > a.screen_ratio ? b.zoom = new THREE.Vector2(b.ratio / a.screen_ratio, 1) : this.ratio < a.screen_ratio ? b.zoom = new THREE.Vector2(1, a.screen_ratio / b.ratio) : b.zoom = new THREE.Vector2(1, 1), c.hasClass("is-video")) {
                let e = jQuery('<video src="' + c.attr("data-src") + '" webkit-playsinline="true" playsinline="true" muted ' + (0 == f ? "autoplay" : "") + " loop/>");
                c.append(e), b.src = new THREE.VideoTexture(e[0])
            } else b.src = a.loader.load(c.attr("data-src"));
            a.gallery.push(b)
        }), this.fragment_shader = `uniform sampler2D currentImage;uniform sampler2D nextImage;uniform vec2 currentZoom;uniform vec2 nextZoom;uniform float progress;uniform float dir;uniform float intensity;uniform vec3 bg_color;varying vec2 vUv;vec2 cUV;vec2 nUV;vec4 c_color;vec4 n_color;float e(float x){return x<0.5?4.0*x*x*x:1.0-pow(-2.0*x+2.0,3.0)*0.5;}float rnd (vec2 st){return fract(sin(dot(st.xy,vec2(12.9798,78.323)))*43758.5453234);}vec2 c(vec2 pos){return (vUv-vec2(0.5))*pos+vec2(0.5);}`, "sliced" == a.options.type ? this.fragment_shader += `const float sd=265.5;const float rA=0.2;const float sC=5.0;mat2 rot2m(float a){float s=sin(a);float c=cos(a);return mat2(c,-s,s,c);}float slices(vec2 p,float count){p*=rot2m(rA);float x=p.x*count;float v=rnd(vec2(floor(x))+sd*count*0.5);return v;}float rndSlices(vec2 p){float s1=1.0,s2=0.0;for(float i=1.0;i<sC;++i){float t=slices(p,2.0*i);s1=min(s1,t);s2=max(s2,t);}return (s1+s2)*0.5;}vec4 pic(float v,vec2 c_uv,vec2 n_uv,vec2 sp){vec4 colA,colB;float m1=(v+1.0);m1=1.0/pow(m1,0.25);float m2=(2.0-v);m2=1.0/pow(m2,0.25);vec2 uv1=(c_uv-sp)*m1+sp;vec2 uv2=(n_uv-sp)*m2+sp;if(c_uv.x<0.0||c_uv.y<0.0||c_uv.x>1.0||c_uv.y>1.0){colA=vec4(bg_color,1.0);}else{colA=texture2D(currentImage,fract(uv1));}if(n_uv.x<0.0||n_uv.y<0.0||n_uv.x>1.0||n_uv.y>1.0){colB=vec4(bg_color,1.0);}else{colB=texture2D(nextImage,fract(uv2));}return mix(colA,colB,v);}void main(){float ep=e(progress);cUV=c(currentZoom);nUV=c(nextZoom);float v=rndSlices(vUv);float b=smoothstep(0.0,v,sin(progress*0.7));vec2 sp=vec2(v,rnd(vec2(v)))*0.5;gl_FragColor=pic(b,cUV,nUV,sp);}` : "pixel" == a.options.type ? this.fragment_shader += `void main() {float ep=e(progress);cUV=c(currentZoom);nUV=c(nextZoom);float offset=rnd(vUv)*intensity;float f=clamp(sin(-0.3+ep*3.7),0.0,1.0);if(cUV.x<0.0||cUV.y<0.0||cUV.x>1.0||cUV.y>1.0) {c_color=vec4(bg_color,1.0);}else{cUV.x+=dir*offset*f;c_color=texture2D(currentImage,cUV);}if (nUV.x<0.0||nUV.y<0.0||nUV.x>1.0||nUV.y>1.0){n_color=vec4(bg_color,1.0);} else {nUV.x-=dir*offset*f;n_color=texture2D(nextImage,nUV);}gl_FragColor=mix(c_color,n_color,ep);}` : "parallax" == a.options.type ? this.fragment_shader += `float ed;void main() {float ep=e(progress);cUV=c(currentZoom);nUV=c(nextZoom);float p2=sin(3.14159265*ep);if(cUV.x<0.0||cUV.y<0.0||cUV.x>1.0||cUV.y>1.0){c_color=vec4(bg_color,1.0);}else{c_color=texture2D(currentImage,cUV+dir*vec2(ep*intensity,0.0));}if(nUV.x<0.0||nUV.y<0.0||nUV.x>1.0||nUV.y>1.0){n_color=vec4(bg_color,1.0);}else{n_color=texture2D(nextImage,nUV+vec2(-intensity*dir+dir*ep*intensity,0.0));}float cu=vUv.y*sin(vUv.y+ep*3.1415*0.25)*0.1*p2;if(dir==1.0){ed=vUv.x+cu;}else{ed=1.0-vUv.x+cu;}float cut=smoothstep(0.5,0.5,ed+ep-0.5);gl_FragColor=mix(c_color,n_color,cut);}` : this.fragment_shader += `void main() {float ep=progress;cUV=c(currentZoom);nUV=c(nextZoom);vec2 dr=vec2(dir*-1.0,0.0);vec2 v=normalize(dr);if (cUV.x<0.0||cUV.y<0.0||cUV.x>1.0||cUV.y>1.0){c_color=vec4(bg_color,1.0);}else{c_color=texture2D(currentImage,cUV);}if(nUV.x<0.0||nUV.y<0.0||nUV.x>1.0||nUV.y>1.0){n_color=vec4(bg_color,1.0);}else{n_color=texture2D(nextImage,nUV);}v/=abs(v.x)+abs(v.y);float d=v.x*0.5+v.y*0.5;float mp=(1.0-step(ep,0.0))*(1.0-smoothstep(-intensity,0.0,v.x*vUv.x+v.y*vUv.y-(d-0.5+ep*(1.0+intensity))));gl_FragColor=mix(c_color,n_color,mp);}`, this.slide_material = new THREE.ShaderMaterial({
            uniforms: {
                currentImage: {value: a.gallery[0].src},
                nextImage: {value: a.gallery[1].src},
                currentZoom: {value: a.gallery[0].zoom},
                nextZoom: {value: a.gallery[1].zoom},
                progress: {value: 0},
                dir: {value: 1},
                intensity: {value: a.options.intensity},
                bg_color: {value: new THREE.Color(a.options.bg_color)}
            },
            fragmentShader: a.fragment_shader,
            vertexShader: `varying vec2 vUv;void main() {vUv=uv;vec4 modelViewPosition=modelViewMatrix*vec4(position,1.0);gl_Position=projectionMatrix*modelViewPosition;}`
        }), this.screen = new THREE.Mesh(new THREE.PlaneBufferGeometry(1, 1, 1, 1), this.slide_material), this.screen.scale.set(window.innerWidth, window.innerHeight, 1), this.scene.add(this.screen), this.active = 0, this.max = this.gallery.length - 1, this.$el.children("div").eq(this.active).addClass("is-active"), a.layout(), this.$el.on("mouseenter", "a.anita-album-link", function () {
            a.cameraFixed = !0
        }).on("mouseleave", "a.anita-album-link", function () {
            a.cameraFixed = !1
        }), this.isTouch = !1, this.moveEvent = {
            dir: 0, current: 0, point: 0, path: 0, targedID: 0, setNextImage: function () {
                a.moveEvent.dir = 1, a.moveEvent.targedID = a.active + 1, a.moveEvent.targedID > a.max && (a.moveEvent.targedID = 0), a.screen.material.uniforms.dir.value = 1, a.screen.material.uniforms.nextImage.value = a.gallery[a.moveEvent.targedID].src, a.screen.material.uniforms.nextZoom.value = a.gallery[a.moveEvent.targedID].zoom
            }, setPrevImage: function () {
                a.moveEvent.dir = -1, a.moveEvent.targedID = a.active - 1, a.moveEvent.targedID < 0 && (a.moveEvent.targedID = a.max), a.screen.material.uniforms.dir.value = -1, a.screen.material.uniforms.nextImage.value = a.gallery[a.moveEvent.targedID].src, a.screen.material.uniforms.nextZoom.value = a.gallery[a.moveEvent.targedID].zoom
            }, f_Start: function (b) {
                0 == a.isChanging && (a.moveEvent.active = !0, a.moveEvent.current = a.active, b.touches ? a.moveEvent.point = b.touches[0].clientX : (a.moveEvent.point = b.clientX, a.$el.parent().addClass("is-grabbed")))
            }, f_Move: function (b) {
                b.preventDefault(), a.moveEvent.active && 0 == a.isChanging && (b.touches ? a.moveEvent.path = (a.moveEvent.point - b.touches[0].clientX) / window.innerWidth : a.moveEvent.path = (a.moveEvent.point - b.clientX) / window.innerWidth, 0 === a.moveEvent.path && (a.moveEvent.dir = 0), 0 === a.moveEvent.dir ? (a.moveEvent.path > 0 && a.moveEvent.setNextImage(), a.moveEvent.path < 0 && a.moveEvent.setPrevImage(), a.screen.material.uniforms.progress.value = 0) : a.moveEvent.dir > 0 && a.moveEvent.path < 0 ? a.moveEvent.setPrevImage() : a.moveEvent.dir < 0 && a.moveEvent.path > 0 ? a.moveEvent.setNextImage() : a.screen.material.uniforms.progress.value = Math.abs(a.moveEvent.path))
            }, f_End: function () {
                let b = 0;
                a.moveEvent.active && (a.moveEvent.path > 0 ? a.moveEvent.path > .2 ? b = !0 : a.isChanging = -1 : a.moveEvent.path < 0 && (a.moveEvent.path < -0.2 ? b = !0 : a.isChanging = -1), b && (a.$el.addClass("is-busy"), a.$el.children(".is-active").children("video").length && a.$el.children(".is-active").children("video")[0].pause(), a.active = a.moveEvent.targedID, a.$el.children(".is-active").removeClass("is-active"), a.$el.children("div").eq(a.active).addClass("is-active"), a.$el.children(".is-active").children("video").length && a.$el.children(".is-active").children("video")[0].play(), a.isChanging = 1), a.moveEvent.active = !1, a.moveEvent.point = 0, a.moveEvent.path = 0, a.moveEvent.dir = 0, a.$el.parent().removeClass("is-grabbed"))
            }
        }, jQuery(window).on("resize", function () {
            a.layout(), setTimeout(function () {
                a.layout()
            }, 300, a)
        }), this.wheelTime = 0, this.$el.parent()[0].addEventListener("wheel", function (b) {
            0 === a.isChanging && (b.timeStamp - a.wheelTime > a.options.wheelSensitive ? (b.deltaY > 0 && a.nextSlide(), b.deltaY < 0 && a.prevSlide()) : b.preventDefault()), a.wheelTime = b.timeStamp
        }), this.$el.parent().on("mousedown", function (b) {
            a.isTouch || a.moveEvent.f_Start(b)
        }), this.$el.parent().on("mousemove", function (b) {
            a.isTouch || a.moveEvent.f_Move(b)
        }), this.$el.parent().on("mouseup", function (b) {
            a.moveEvent.f_End()
        }), this.$el.parent().on("mouseleave", function (b) {
            a.moveEvent.f_End()
        }), this.$el.parent().on("touchstart", function (b) {
            a.isTouch = !0, a.moveEvent.f_Start(b)
        }), this.$el.parent().on("touchmove", function (b) {
            a.moveEvent.f_Move(b)
        }), this.$el.parent().on("touchend", function () {
            a.moveEvent.f_End()
        }), !0 == this.options.nav && (this.$el.append(`<a href="#" class="anita-gallery-nav anita-gallery-nav__prev">` + (a.$el.attr("data-prev-label") ? "<span>" + a.$el.attr("data-prev-label") + "</span>" : "") + `</a><a href="#" class="anita-gallery-nav anita-gallery-nav__next">` + (a.$el.attr("data-next-label") ? "<span>" + a.$el.attr("data-next-label") + "</span>" : "") + `</a>`), this.$el.on("click", ".anita-gallery-nav__prev", function (b) {
            b.preventDefault(), a.prevSlide()
        }), this.$el.on("click", ".anita-gallery-nav__next", function (b) {
            b.preventDefault(), a.nextSlide()
        })), jQuery(document).on("keyup", function (b) {
            37 == b.keyCode && a.prevSlide(), 39 == b.keyCode && a.nextSlide()
        }), this.$el.on("click", ".anita-album-link", function () {
            window.localStorage.setItem("anita_listing_index", a.active)
        }), this.$el.parent().hasClass("anita-albums-listing") && null !== window.localStorage.getItem("anita_back_from_album") && null !== window.localStorage.getItem("anita_listing_index") && (!0 == JSON.parse(window.localStorage.getItem("anita_back_from_album")) && (this.active = parseInt(JSON.parse(window.localStorage.getItem("anita_listing_index")), 10), this.changeSlide()), window.localStorage.setItem("anita_back_from_album", !1), window.localStorage.setItem("anita_listing_index", "0")), this.$el.parent().addClass("is-loaded"), setTimeout(function () {
            jQuery(a.renderer.domElement).addClass("is-loaded")
        }, 100, a), this.anim = requestAnimationFrame(() => this.animate())
    }

    prevSlide() {
        this.isChanging || (this.active -= 1, this.active < 0 && (this.active = this.max), this.screen.material.uniforms.dir.value = -1, this.changeSlide())
    }

    nextSlide() {
        this.isChanging || (this.active += 1, this.active > this.max && (this.active = 0), this.screen.material.uniforms.dir.value = 1, this.changeSlide())
    }

    changeSlide() {
        this.isChanging || (this.$el.addClass("is-busy"), this.$el.children(".is-active").children("video").length && this.$el.children(".is-active").children("video")[0].pause(), this.$el.children(".is-active").removeClass("is-active"), this.$el.children("div").eq(this.active).addClass("is-active"), this.$el.children(".is-active").children("video").length && this.$el.children(".is-active").children("video")[0].play(), this.screen.material.uniforms.nextImage.value = this.gallery[this.active].src, this.screen.material.uniforms.nextZoom.value = this.gallery[this.active].zoom, this.isChanging = 1)
    }

    layout() {
        let c = this, b = window.innerWidth, a = window.innerHeight;
        this.renderer.setSize(b, a), this.renderer.setSize(b, a), this.camera.fov = 180 * (2 * Math.atan(a / 2 / this.options.perspective)) / Math.PI, this.camera.aspect = b / a, this.camera.updateProjectionMatrix(), this.screen_ratio = a / b, this.screen.scale.set(window.innerWidth, window.innerHeight, 1), jQuery(this.gallery).each(function (a) {
            "cover" == c.options.fit ? this.ratio > c.screen_ratio ? (this.zoom.x = 1, this.zoom.y = c.screen_ratio / this.ratio) : this.ratio < c.screen_ratio && (this.zoom.x = this.ratio / c.screen_ratio, this.zoom.y = 1) : this.ratio > c.screen_ratio ? (this.zoom.x = this.ratio / c.screen_ratio, this.zoom.y = 1) : this.ratio < c.screen_ratio && (this.zoom.x = 1, this.zoom.y = c.screen_ratio / this.ratio), a == c.active && (c.screen.material.uniforms.nextZoom.value = this.zoom)
        })
    }

    animate() {
        "undefined" != typeof stats && stats.begin(), this.anim = requestAnimationFrame(() => this.animate());
        let b = this.clock.getElapsedTime(), a = b - this.prevTime;
        this.prevTime = b, 1 == this.isChanging && (this.screen.material.uniforms.progress.value += (1.5 - this.screen.material.uniforms.progress.value) * 1.2 * a, this.screen.material.uniforms.progress.value >= 1 && (this.screen.material.uniforms.currentImage.value = this.gallery[this.active].src, this.screen.material.uniforms.currentZoom.value = this.gallery[this.active].zoom, this.screen.material.uniforms.progress.value = 0, this.isChanging = 0, this.$el.removeClass("is-busy"))), -1 == this.isChanging && (this.screen.material.uniforms.progress.value += (-0.5 - this.screen.material.uniforms.progress.value) * 1.2 * a, this.screen.material.uniforms.progress.value <= 0 && (this.screen.material.uniforms.progress.value = 0, this.isChanging = 0)), this.cameraFixed ? this.camera.position.z += (this.options.zoomed - this.camera.position.z) * a : jQuery("body").hasClass("anita-show-menu") ? this.camera.position.z += (this.options.zoomed - this.camera.position.z) * a * 2 : this.camera.position.z += (this.options.perspective - this.camera.position.z) * a, this.camera.position.z > this.options.perspective && (this.camera.position.z = this.options.perspective), this.camera.position.z < this.options.zoomed && (this.camera.position.z = this.options.zoomed), this.renderer.render(this.scene, this.camera), "undefined" != typeof stats && stats.end()
    }
}

jQuery(".anita-gl-slider-gallery").length && jQuery.getScript("js/lib/three.min.js").done(function () {
    let a = jQuery(".anita-gl-slider-gallery"), b = {
        container: jQuery(".anita-main"),
        type: a.attr("data-type") ? a.attr("data-type") : "fade",
        fit: a.attr("data-fit") ? a.attr("data-fit") : "cover",
        intensity: a.attr("data-intensity") ? a.attr("data-intensity") : "0.5",
        bg_color: a.attr("data-bgcolor") ? a.attr("data-bgcolor") : "#000000",
        nav: !a.attr("data-nav") || a.attr("data-nav")
    };
    new Anita_GL_Slider(a, b)
})
