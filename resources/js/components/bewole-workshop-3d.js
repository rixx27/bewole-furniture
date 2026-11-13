import * as THREE from 'three'
import { OrbitControls } from 'three/examples/jsm/controls/OrbitControls.js'
import { RoundedBoxGeometry } from 'three/examples/jsm/geometries/RoundedBoxGeometry.js'
import { mergeGeometries } from 'three/examples/jsm/utils/BufferGeometryUtils.js'
import { RoomEnvironment } from 'three/examples/jsm/environments/RoomEnvironment.js'

/**
 * Bewole Jepara Furniture — 3D Artisan Workshop Diorama
 *
 * Diorama 3D prosedural interaktif alur pembuatan furniture kustom Jepara:
 * 01. Konsultasi & Blueprint
 * 02. Seleksi Kayu Jati Perhutani & Milling
 * 03. Konstruksi Sambungan Purus & Pasak (Mortise & Tenon)
 * 04. Amplas Halus & Finishing Eco PU
 * 05. QC Passed, Peti Kayu Palet & Lacak Pengiriman
 *
 * Dibangun menggunakan Three.js prosedural (tanpa file model eksternal berat).
 */

export function bewoleWorkshop3d() {
    return {
        mode: 'assembled',
        cameraMode: 'overview',
        isPlaying: true,
        selectedStation: null,
        journeyStep: 0,
        journeyTitle: 'Menerima Request Desain',
        journeyDetail: 'Studio Desain · Menganalisis sketsa & spesifikasi custom Anda',
        journeyProgress: '0%',
        _dispose: null,

        init() {
            const root = this.$el
            if (!root) return

            // Tunggu font siap agar render kanvas 2D tajam
            document.fonts.ready.then(() => {
                this._dispose = initBewoleScene(root, {
                    onStation: (id) => {
                        this.selectedStation = id
                    },
                    onJourneyUpdate: (step, title, detail, progress) => {
                        this.journeyStep = step
                        this.journeyTitle = title
                        this.journeyDetail = detail
                        this.journeyProgress = progress
                    },
                })
            })
        },

        destroy() {
            if (this._dispose) {
                this._dispose()
                this._dispose = null
            }
        },

        setMode(newMode) {
            this.mode = newMode
            window.__bewoleMachine?.setMode(newMode)
        },

        setCamera(newCamera) {
            this.cameraMode = newCamera
            window.__bewoleMachine?.setCamera(newCamera)
        },

        togglePlay() {
            this.isPlaying = !this.isPlaying
            if (this.isPlaying) {
                window.__bewoleMachine?.play()
            } else {
                window.__bewoleMachine?.pause()
            }
        },
    }
}

export function initBewoleScene(root, options = {}) {
    const cleanups = []
    const frameWidth = () => root.clientWidth || 600
    const frameHeight = () => root.clientHeight || 450

    const listen = (target, type, handler) => {
        target.addEventListener(type, handler)
        cleanups.push(() => target.removeEventListener(type, handler))
    }

    const $ = (selector) => root.querySelector(selector)

    const sceneEl = $('.bewole-scene-canvas')
    if (!sceneEl) return () => {}

    const TAU = Math.PI * 2
    const scene = new THREE.Scene()
    const camera = new THREE.PerspectiveCamera(34, frameWidth() / frameHeight(), 0.1, 150)

    let renderer
    try {
        renderer = new THREE.WebGLRenderer({
            antialias: true,
            alpha: true,
            powerPreference: 'high-performance',
        })
    } catch (e) {
        console.error('WebGL tidak tersedia:', e)
        return () => {}
    }

    renderer.setClearColor(0x000000, 0)
    renderer.setPixelRatio(Math.min(window.devicePixelRatio || 1, 1.75))
    renderer.setSize(frameWidth(), frameHeight())
    renderer.outputColorSpace = THREE.SRGBColorSpace
    renderer.toneMapping = THREE.ACESFilmicToneMapping
    renderer.toneMappingExposure = 1.15
    renderer.shadowMap.enabled = true
    renderer.shadowMap.type = THREE.PCFShadowMap
    renderer.localClippingEnabled = true
    sceneEl.appendChild(renderer.domElement)

    const controls = new OrbitControls(camera, renderer.domElement)
    controls.enableDamping = true
    controls.dampingFactor = 0.065
    controls.enablePan = false
    controls.minDistance = 7
    controls.maxDistance = 50
    controls.minPolarAngle = 0.1
    controls.maxPolarAngle = Math.PI * 0.47
    controls.rotateSpeed = 0.5
    controls.zoomSpeed = 0.7

    // Jangan rampas gesture scroll vertikal di smartphone
    if (window.matchMedia('(pointer:coarse)').matches) {
        controls.enableZoom = false
    }

    // Studio Lighting bernuansa Atelier Kayu Mewah
    const pmrem = new THREE.PMREMGenerator(renderer)
    const room = new RoomEnvironment()
    const env = pmrem.fromScene(room, 0.04)
    scene.environment = env.texture
    scene.environmentIntensity = 0.55
    room.dispose()
    pmrem.dispose()

    scene.add(new THREE.HemisphereLight(0xfff5eb, 0x241d19, 2.2))

    // Key light hangat (mentari jendela workshop)
    const key = new THREE.DirectionalLight(0xffecd0, 4.0)
    key.position.set(-5, 12, 7)
    key.castShadow = true
    key.shadow.mapSize.set(2048, 2048)
    Object.assign(key.shadow.camera, {
        left: -10,
        right: 10,
        top: 9,
        bottom: -9,
        near: 0.5,
        far: 35,
    })
    key.shadow.normalBias = 0.035
    key.shadow.bias = -0.0002
    key.shadow.radius = 4
    scene.add(key)

    // Rim light kebiruan lembut
    const rim = new THREE.DirectionalLight(0xdde8f5, 2.8)
    rim.position.set(4, 7, -8)
    scene.add(rim)

    // Warm amber glow (lampu gantung atelier)
    const warm = new THREE.PointLight(0xffaa33, 26, 22, 2)
    warm.position.set(-4, 5, 3)
    scene.add(warm)

    // Material palette khas Bewole Jepara
    const mat = (color, metalness = 0.1, roughness = 0.4, extra = {}) =>
        new THREE.MeshStandardMaterial({ color, metalness, roughness, ...extra })

    const M = {
        // Kayu Jati gelap untuk base
        darkTeak: mat(0x231a14, 0.15, 0.45),
        // Kayu Jati natural hangat
        teakWood: mat(0x8a5327, 0.05, 0.4),
        // Jati muda / Honey Teak
        honeyTeak: mat(0xc9873d, 0.05, 0.35),
        // Kuningan antik / Brass Jepara
        brass: mat(0xd4af37, 0.85, 0.22),
        // Besi / Steel alat tukang
        steel: mat(0x525964, 0.88, 0.22),
        // Chrome halus
        chrome: mat(0xcbd2d8, 0.92, 0.18),
        // Kertas gambar blueprint krem
        paper: mat(0xf7f4ec, 0, 0.88),
        // Cokelat tua pekat
        deepEbony: mat(0x130e0c, 0.2, 0.5),
        // Lampu indikator emas/amber
        amberGlow: mat(0xff9f43, 0.2, 0.25, { emissive: 0xff8c1a, emissiveIntensity: 1.6 }),
        // Indikator hijau QC passed
        greenGlow: mat(0x629c69, 0.1, 0.3, { emissive: 0x4e8555, emissiveIntensity: 1.1 }),
        // Kaca / Acrylic transparan
        glass: mat(0x99aab3, 0.5, 0.15, { transparent: true, opacity: 0.25, depthWrite: false }),
    }

    const geometries = new Map()
    function boxGeo(w, h, d, r = 0.04) {
        const k = `b${w},${h},${d},${r}`
        if (!geometries.has(k)) {
            geometries.set(
                k,
                r
                    ? new RoundedBoxGeometry(w, h, d, 2, Math.min(r, w / 3, h / 3, d / 3))
                    : new THREE.BoxGeometry(w, h, d)
            )
        }
        return geometries.get(k)
    }

    function box(parent, w, h, d, x, y, z, m = M.darkTeak, r = 0.04) {
        const o = new THREE.Mesh(boxGeo(w, h, d, r), m)
        o.position.set(x, y, z)
        o.castShadow = true
        o.receiveShadow = true
        parent.add(o)
        return o
    }

    function cyl(parent, r, h, x, y, z, m = M.chrome, r2 = r, segments = 24) {
        const k = `c${r},${r2},${h},${segments}`
        if (!geometries.has(k)) geometries.set(k, new THREE.CylinderGeometry(r, r2, h, segments))
        const o = new THREE.Mesh(geometries.get(k), m)
        o.position.set(x, y, z)
        o.castShadow = true
        o.receiveShadow = true
        parent.add(o)
        return o
    }

    function tube(parent, pts, r, m = M.brass) {
        const curve = new THREE.CatmullRomCurve3(pts.map((p) => new THREE.Vector3(...p)))
        const o = new THREE.Mesh(
            new THREE.TubeGeometry(curve, Math.max(12, pts.length * 7), r, 8, false),
            m
        )
        o.castShadow = true
        parent.add(o)
        return o
    }

    function canvasTexture(w, h, draw) {
        const c = document.createElement('canvas')
        c.width = w
        c.height = h
        const ctx = c.getContext('2d')
        draw(ctx, w, h)
        const t = new THREE.CanvasTexture(c)
        t.colorSpace = THREE.SRGBColorSpace
        return { texture: t, canvas: c, ctx }
    }

    function print(ctx, txt, x, y, size = 20, color = '#f7f4ec', weight = 600, font = 'serif') {
        ctx.fillStyle = color
        ctx.font = `${weight} ${size}px ${font === 'serif' ? 'Cinzel, Georgia, serif' : 'system-ui, sans-serif'}`
        ctx.fillText(txt, x, y)
    }

    function screen(parent, w, h, x, y, z, tex) {
        const map = 'texture' in tex ? tex.texture : tex
        const o = new THREE.Mesh(
            new THREE.PlaneGeometry(w, h),
            new THREE.MeshBasicMaterial({ map, toneMapped: false })
        )
        o.position.set(x, y, z)
        parent.add(o)
        return o
    }

    const machine = new THREE.Group()
    scene.add(machine)

    // Lantai bayangan
    const floor = new THREE.Mesh(
        new THREE.PlaneGeometry(70, 70),
        new THREE.ShadowMaterial({ opacity: 0.22 })
    )
    floor.rotation.x = -Math.PI / 2
    floor.position.y = -0.43
    floor.receiveShadow = true
    scene.add(floor)

    // Base plate meja kerja kayu jati mewah dengan lis kuningan
    box(machine, 12.8, 0.38, 8.25, 0, -0.09, 0, M.darkTeak, 0.17)
    box(machine, 12.6, 0.055, 8.08, 0, 0.13, 0, M.brass, 0.11)
    box(machine, 12.49, 0.09, 7.96, 0, 0.19, 0, M.teakWood, 0.1)
    box(machine, 12.55, 0.027, 8.02, 0, -0.19, 0, M.deepEbony, 0.06)
    box(machine, 11.9, 0.026, 0.032, 0, -0.17, 4.115, M.amberGlow, 0.01)

    // Kaki bantalan meja kayu
    for (const x of [-5.6, 5.6]) {
        for (const z of [-3.35, 3.35]) {
            cyl(machine, 0.39, 0.25, x, -0.31, z, M.brass)
            cyl(machine, 0.29, 0.09, x, -0.4, z, M.deepEbony)
        }
    }

    // Plakat kuningan grafir depan meja
    const plaqueTexture = canvasTexture(1536, 176, (c, w, h) => {
        c.fillStyle = '#211710'
        c.fillRect(0, 0, w, h)
        c.strokeStyle = '#c59b27'
        c.lineWidth = 3
        c.strokeRect(4, 4, w - 8, h - 8)
        print(c, 'BEWOLE JEPARA', 45, 78, 42, '#f5deb3', 700, 'serif')
        print(c, '·  KAYU JATI SOLID  ·  KONSTRUKSI PURUS  ·  FINISHING ALAMI', 380, 76, 26, '#d8c49d', 500, 'sans')
        print(c, 'ATELIER FURNITURE KUSTOM    /    5 TAHAP KERAJINAN TANGAN', 47, 134, 18, '#a89373', 600, 'sans')
        print(c, 'CRAFT JOURNEY NO. 01', 1230, 130, 21, '#e6b94d', 700, 'serif')
    })
    const plaque = screen(machine, 7.35, 0.84, -0.4, 0.25, 3.51, plaqueTexture)
    plaque.rotation.x = -Math.PI / 2

    // Definisi 5 Stasiun Kustomisasi Bewole
    const definitions = [
        {
            id: 'design',
            name: '01. Konsultasi & Blueprint',
            step: 1,
            output: 'Sketsa 3D',
            pos: [4.03, 0.29, 0.12],
            desc: 'Analisis foto inspirasi, ukuran ruangan, & pembuatan cetak biru teknis 3D.',
        },
        {
            id: 'milling',
            name: '02. Seleksi Jati & Milling',
            step: 2,
            output: 'Balok Presisi',
            pos: [-4.15, 0.29, -0.65],
            desc: 'Kayu jati Perhutani Grade A kering oven (MC 10-12%) dipotong presisi.',
        },
        {
            id: 'joinery',
            name: '03. Sambungan Purus Jepara',
            step: 3,
            output: 'Rangka Purus',
            pos: [-1.65, 0.29, -2.03],
            desc: 'Pengrajin Jepara merakit sambungan purus & pasak (mortise & tenon) tanpa paku biasa.',
        },
        {
            id: 'finishing',
            name: '04. Amplas & Eco Finishing',
            step: 4,
            output: 'Finishing Satin',
            pos: [1.5, 0.29, -2.08],
            desc: 'Amplas bertingkat grit 120-400 & top coat PU Matte tahan gores serta air.',
        },
        {
            id: 'dispatch',
            name: '05. QC & Peti Palet Kayu',
            step: 5,
            output: 'Siap Kirim',
            pos: [0.93, 0.29, 1.85],
            desc: 'Pemeriksaan 100%, pembungkusan foam tebal & peti kayu kokoh siap kirim kargo.',
        },
    ]

    const stations = []
    const cutPlane = new THREE.Plane(new THREE.Vector3(0, -1, 0), 10)
    const shellMaterials = []

    function shell(m) {
        const s = m.clone()
        s.clippingPlanes = [cutPlane]
        s.clipShadows = true
        s.side = THREE.DoubleSide
        shellMaterials.push(s)
        return s
    }

    const S = {
        body: shell(M.darkTeak),
        wood: shell(M.teakWood),
        brass: shell(M.brass),
        amber: shell(M.honeyTeak),
    }

    const labelsContainer = $('.bewole-labels-container')

    definitions.forEach((d, i) => {
        const group = new THREE.Group()
        group.position.fromArray(d.pos)
        machine.add(group)

        const glowMat = M.amberGlow.clone()
        glowMat.emissiveIntensity = 0.55

        // Dudukan stasiun dengan aksen kuningan & jati
        box(group, 2.05, 0.12, 1.78, 0, 0.03, 0, M.deepEbony, 0.1)
        box(group, 1.97, 0.03, 1.7, 0, 0.12, 0, glowMat, 0.09)
        box(group, 2.03, 0.17, 1.75, 0, 0.215, 0, M.teakWood, 0.1)

        // Label plat nomor stasiun
        const stPlaque = canvasTexture(512, 116, (c, w, h) => {
            c.fillStyle = '#1c130d'
            c.fillRect(0, 0, w, h)
            c.strokeStyle = '#c59b27'
            c.lineWidth = 2
            c.strokeRect(2, 2, w - 4, h - 4)
            print(c, String(i + 1).padStart(2, '0'), 22, 78, 44, '#e6b94d', 700, 'serif')
            print(c, d.name.toUpperCase(), 110, 73, 28, '#f4ece1', 600, 'sans')
        })
        screen(group, 1.54, 0.345, 0, 0.27, 0.891, stPlaque)

        // DOM Label untuk overlay floating
        let labelEl = null
        if (labelsContainer) {
            labelEl = document.createElement('div')
            labelEl.className = 'bewole-station-label'
            labelEl.innerHTML = `
                <div class="stem"></div>
                <div class="label-card">
                    <div class="label-title">
                        <span>${String(i + 1).padStart(2, '0')}</span>${d.name}
                    </div>
                    <div class="label-meta">Tahap ${d.step} · ${d.output}</div>
                </div>
            `
            labelsContainer.appendChild(labelEl)
            cleanups.push(() => labelEl.remove())
        }

        stations.push({
            ...d,
            group,
            base: new THREE.Vector3(...d.pos),
            glowMat,
            label: labelEl,
            index: i,
            anchor: new THREE.Vector3(0, 2.5, 0),
        })
    })

    // -------------------------------------------------------------
    // STASIUN 1: KONSULTASI & BLUEPRINT (pos: [4.03, 0.29, 0.12])
    // -------------------------------------------------------------
    const stDesign = stations[0].group
    box(stDesign, 1.75, 1.77, 1.02, 0, 1.24, -0.13, S.wood, 0.12)
    box(stDesign, 1.58, 0.13, 1.09, 0, 2.16, -0.13, S.brass, 0.04)
    box(stDesign, 1.47, 1.38, 0.055, 0, 1.31, 0.405, M.deepEbony, 0.025)

    // Tekstur Blueprint Sketsa Meja Custom
    const blueprintTex = canvasTexture(480, 460, (c, w, h) => {
        c.fillStyle = '#0f2438'
        c.fillRect(0, 0, w, h)
        c.strokeStyle = '#346b9a'
        c.lineWidth = 1
        for (let x = 20; x < w; x += 25) {
            c.beginPath()
            c.moveTo(x, 0)
            c.lineTo(x, h)
            c.stroke()
        }
        for (let y = 20; y < h; y += 25) {
            c.beginPath()
            c.moveTo(0, y)
            c.lineTo(w, y)
            c.stroke()
        }
        print(c, 'SKETSA CUSTOM CLIENT', 30, 50, 26, '#e2f0fc', 700, 'sans')
        print(c, 'Tipe: Meja Makan Jati Oval 220x100cm', 30, 80, 15, '#89b6db', 500, 'sans')

        // Gambar sketsa meja di blueprint
        c.strokeStyle = '#ffffff'
        c.lineWidth = 3
        c.strokeRect(80, 140, 320, 110)
        c.fillStyle = '#1d486d'
        c.fillRect(80, 140, 320, 110)
        c.strokeRect(110, 250, 40, 120)
        c.strokeRect(330, 250, 40, 120)
        print(c, '✦ Kustom Dimensi & Kaki Bundar', 95, 205, 18, '#ffffff', 600, 'sans')
        print(c, 'STATUS: APPROVED FOR MILLING', 110, 420, 18, '#4ade80', 700, 'sans')
    })
    screen(stDesign, 1.31, 1.255, 0, 1.37, 0.44, blueprintTex)

    // Kartu pesanan baru melayang
    const incomingOrder = new THREE.Group()
    incomingOrder.userData.moving = true
    stDesign.add(incomingOrder)
    box(incomingOrder, 0.86, 0.42, 0.043, 0, 0, 0, M.amberGlow, 0.035)
    const orderCardTex = canvasTexture(432, 204, (c, w, h) => {
        c.fillStyle = '#b45309'
        c.fillRect(0, 0, w, h)
        c.fillStyle = '#26170d'
        c.beginPath()
        c.arc(58, 98, 32, 0, TAU)
        c.fill()
        print(c, 'B', 43, 112, 34, '#f5deb3', 700, 'serif')
        print(c, 'Order Kustom', 108, 88, 32, '#fef3c7', 700, 'sans')
        print(c, 'Dimensi Ruang Spesifik', 108, 134, 20, '#fde68a', 500, 'sans')
    })
    screen(incomingOrder, 0.82, 0.39, 0, 0, 0.026, orderCardTex)

    // -------------------------------------------------------------
    // STASIUN 2: SELEKSI KAYU JATI & MILLING (pos: [-4.15, 0.29, -0.65])
    // -------------------------------------------------------------
    const stMilling = stations[1].group
    box(stMilling, 1.74, 0.62, 1.33, 0, 0.65, -0.05, S.wood, 0.13)
    box(stMilling, 1.5, 0.1, 1.16, 0, 0.99, -0.04, S.body, 0.025)

    // Tiang gantry pemotong kayu
    for (const x of [-0.68, 0.68]) {
        cyl(stMilling, 0.065, 1.73, x, 1.35, -0.18, M.steel)
        box(stMilling, 0.22, 1.8, 0.22, x, 1.37, -0.44, S.brass, 0.035)
    }
    box(stMilling, 1.82, 0.27, 0.4, 0, 2.28, -0.35, S.amber, 0.045)

    // Bilah gergaji piringan baja (Circular Saw Blade)
    const sawBlade = new THREE.Group()
    stMilling.add(sawBlade)
    sawBlade.position.set(0, 1.9, -0.08)
    sawBlade.userData.moving = true
    box(sawBlade, 0.45, 0.36, 0.44, 0, 0, 0, M.deepEbony, 0.05)
    // Piringan gergaji baja berputar
    const bladeDisc = cyl(sawBlade, 0.35, 0.03, 0, -0.22, 0.03, M.steel, 0.35, 32)
    bladeDisc.rotation.z = Math.PI / 2
    sawBlade.userData.bladeDisc = bladeDisc

    // Balok kayu jati utuh yang sedang dibelah
    const woodLog = box(stMilling, 1.3, 0.45, 0.55, 0, 1.25, 0.5, M.teakWood, 0.04)
    woodLog.userData.moving = true

    // -------------------------------------------------------------
    // STASIUN 3: KONSTRUKSI SAMBUNGAN PURUS (pos: [-1.65, 0.29, -2.03])
    // -------------------------------------------------------------
    const stJoinery = stations[2].group
    box(stJoinery, 1.9, 0.66, 1.36, 0, 0.67, -0.04, S.body, 0.11)

    // Meja kerja arsitektural sambungan kayu
    const joineryTop = new THREE.Group()
    joineryTop.position.set(0, 1.01, -0.08)
    joineryTop.rotation.x = -0.32
    stJoinery.add(joineryTop)
    box(joineryTop, 1.76, 0.12, 1.27, 0, 0, 0, S.wood, 0.04)

    // Layar panduan anatomi sambungan Purus (Mortise & Tenon)
    const joineryScreen = canvasTexture(640, 340, (c, w, h) => {
        c.fillStyle = '#1c1511'
        c.fillRect(0, 0, w, h)
        print(c, 'ANATOMI PURUS & PASAK', 25, 45, 23, '#f5deb3', 700, 'serif')
        print(c, 'JEPARA JOINERY SPEC', 440, 45, 17, '#e6b94d', 600, 'sans')

        // Diagram sambungan kayu saling mengunci
        c.fillStyle = '#8a5327'
        c.fillRect(40, 80, 220, 60)
        c.fillStyle = '#c9873d'
        c.fillRect(260, 95, 90, 30) // Tenon (Lidah Purus)

        c.fillStyle = '#5c3a21'
        c.fillRect(380, 70, 210, 190) // Mortise (Lubang)
        c.fillStyle = '#1c1511'
        c.fillRect(380, 110, 90, 32) // Lubang tembus

        print(c, '✦ Lidah Purus (Tenon): Mengunci rapat ke lubang kayu', 40, 220, 19, '#f4ece1', 500, 'sans')
        print(c, '✦ Pasak Jati Alami: Tanpa paku berkarat, tahan 50+ tahun', 40, 255, 19, '#f4ece1', 500, 'sans')
        print(c, 'PRESISI TOLERANSI: 0.1 mm', 40, 305, 19, '#4ade80', 700, 'sans')
    })
    const jsPlane = screen(joineryTop, 1.53, 0.79, 0, 0.067, -0.17, joineryScreen)
    jsPlane.rotation.x = -Math.PI / 2

    // Klem kayu dan pahat ukir mini
    for (let i = 0; i < 3; i++) {
        const x = -0.5 + i * 0.5
        cyl(joineryTop, 0.07, 0.04, x, 0.1, 0.45, M.brass)
        const t = cyl(joineryTop, 0.025, 0.16, x, 0.19, 0.45, M.steel)
        t.rotation.x = -0.4
    }

    // -------------------------------------------------------------
    // STASIUN 4: FINISHING & COATING LAB (pos: [1.5, 0.29, -2.08])
    // -------------------------------------------------------------
    const stFinishing = stations[3].group
    box(stFinishing, 1.35, 0.18, 0.88, 0, 0.44, 0, S.brass, 0.045)
    box(stFinishing, 2.42, 1.72, 0.2, 0, 1.94, -0.17, S.wood, 0.08)
    box(stFinishing, 2.28, 1.59, 0.1, 0, 1.94, -0.044, M.deepEbony, 0.045)

    // Layar spesifikasi finishing ramah lingkungan
    const finishingTex = canvasTexture(896, 592, (c, w, h) => {
        c.fillStyle = '#241a14'
        c.fillRect(0, 0, w, h)
        c.fillStyle = '#3a271c'
        c.fillRect(0, 0, w, 56)
        print(c, 'BEWOLE COATING LAB', 33, 38, 22, '#f5deb3', 700, 'serif')
        print(c, 'Natural Water-Based Polyurethane', 480, 38, 16, '#c9873d', 500, 'sans')

        // Kartu contoh finishing
        c.fillStyle = '#8a5327'
        c.beginPath()
        c.roundRect(32, 100, 270, 420, 12)
        c.fill()
        print(c, 'NATURAL TEAK', 50, 150, 28, '#ffffff', 700, 'serif')
        print(c, 'Serat Kayu Jati Menonjol', 50, 190, 18, '#fde68a', 500, 'sans')

        // Fitur keunggulan
        c.fillStyle = '#f4ece1'
        print(c, 'Tahapan Pelapisan:', 340, 140, 26, '#f5deb3', 700, 'serif')
        print(c, '1. Sanding Halus Grit 120 s/d 400', 340, 190, 20, '#d1c4b2', 500, 'sans')
        print(c, '2. Wood Filler & Pore Sealer Organik', 340, 235, 20, '#d1c4b2', 500, 'sans')
        print(c, '3. Natural Teak Stain (Bebas Bau Toksik)', 340, 280, 20, '#d1c4b2', 500, 'sans')
        print(c, '4. Top Coat Matte / Satin Anti-Gores', 340, 325, 20, '#d1c4b2', 500, 'sans')
        print(c, '✦ TAHAN AIR, PANAS & GORESAN ✦', 340, 410, 22, '#4ade80', 700, 'sans')
    })
    screen(stFinishing, 2.16, 1.43, 0, 1.95, 0.011, finishingTex)

    // Nozel spray gun finishing bergerak
    const sprayNozzle = new THREE.Group()
    sprayNozzle.userData.moving = true
    stFinishing.add(sprayNozzle)
    box(sprayNozzle, 0.35, 0.5, 0.15, 0.94, 0.81, 0.55, M.brass, 0.02)
    cyl(sprayNozzle, 0.04, 0.25, 0.94, 0.55, 0.55, M.chrome)

    // -------------------------------------------------------------
    // STASIUN 5: QC, PACKING PETI & EKSPEDISI (pos: [0.93, 0.29, 1.85])
    // -------------------------------------------------------------
    const stDispatch = stations[4].group
    box(stDispatch, 1.91, 0.63, 1.32, 0, 0.65, -0.06, S.wood, 0.12)
    box(stDispatch, 1.96, 0.19, 1.38, 0, 0.42, -0.04, S.body, 0.04)

    // Layar Status QC Passed & Lacak Pesanan
    box(stDispatch, 1.13, 0.46, 0.19, -0.35, 1.98, -0.56, S.body, 0.045)
    const qcTex = canvasTexture(512, 176, (c, w, h) => {
        c.fillStyle = '#15241b'
        c.fillRect(0, 0, 512, 176)
        print(c, 'QUALITY CONTROL 100%', 22, 44, 21, '#86efac', 600, 'sans')
        print(c, 'STATUS: QC PASSED ✓', 26, 110, 38, '#4ade80', 700, 'sans')
        print(c, 'TERHUBUNG KE SISTEM LACAK PESANAN', 26, 148, 16, '#bbf7d0', 500, 'sans')
    })
    screen(stDispatch, 1.015, 0.349, -0.35, 1.98, -0.459, qcTex)

    // Peti Kayu Palet Kargo Siap Kirim (Wooden Crate)
    const woodenCrate = new THREE.Group()
    woodenCrate.position.set(0.59, 1.25, -0.1)
    woodenCrate.userData.moving = true
    stDispatch.add(woodenCrate)
    box(woodenCrate, 0.72, 0.75, 0.65, 0, 0, 0, M.honeyTeak, 0.03)

    // Bilah palet pelindung
    for (let p = -0.3; p <= 0.3; p += 0.2) {
        box(woodenCrate, 0.74, 0.06, 0.03, 0, p, 0.33, M.teakWood, 0.01)
        box(woodenCrate, 0.03, 0.06, 0.66, 0.37, p, 0, M.teakWood, 0.01)
    }

    // Label Pengiriman Fragile
    const labelTex = canvasTexture(256, 256, (c, w, h) => {
        c.fillStyle = '#f7f4ec'
        c.fillRect(0, 0, w, h)
        c.fillStyle = '#dc2626'
        c.fillRect(0, 0, w, 40)
        print(c, 'FRAGILE', 70, 28, 22, '#ffffff', 800, 'sans')
        print(c, 'BEWOLE JEPARA', 25, 75, 20, '#1c130d', 700, 'serif')
        print(c, 'Ekspedisi Kargo Terpercaya', 25, 105, 14, '#57483c', 500, 'sans')
        print(c, 'Palet Kayu Tertutup Aman', 25, 130, 14, '#57483c', 500, 'sans')
        // Barcode
        c.fillStyle = '#1c130d'
        for (let b = 25; b < 230; b += 6) {
            c.fillRect(b, 160, b % 12 === 0 ? 4 : 2, 60)
        }
        print(c, 'TRK-BWL-2026-001', 50, 240, 14, '#57483c', 600, 'monospace')
    })
    const crateLabel = screen(woodenCrate, 0.45, 0.45, 0, 0, 0.34, labelTex)
    crateLabel.material.side = THREE.DoubleSide

    // -------------------------------------------------------------
    // CONVEYOR TRACK (Jalur Alur Kerajinan)
    // -------------------------------------------------------------
    const path = new THREE.CatmullRomCurve3(
        [
            [-3.95, 0.84, 0.65],
            [-3.1, 0.84, -0.12],
            [-1.45, 0.84, -0.79],
            [1.32, 0.84, -0.8],
            [3.3, 0.84, 0.19],
            [3.43, 0.84, 1.21],
            [1.35, 0.84, 2.7],
            [-1.4, 0.84, 2.52],
            [-3.54, 0.84, 1.65],
        ].map((p) => new THREE.Vector3(...p)),
        true,
        'catmullrom',
        0.25
    )

    const belt = new THREE.Group()
    machine.add(belt)
    const frameMesh = new THREE.Mesh(new THREE.TubeGeometry(path, 150, 0.35, 8, true), M.deepEbony)
    frameMesh.scale.y = 0.3
    frameMesh.position.y = 0.51
    belt.add(frameMesh)

    // Slat kayu conveyor
    const beltCount = 140
    const beltSlats = new THREE.InstancedMesh(boxGeo(0.14, 0.065, 0.63, 0.012), M.teakWood, beltCount)
    beltSlats.receiveShadow = true
    belt.add(beltSlats)
    beltSlats.instanceMatrix.setUsage(THREE.DynamicDrawUsage)

    const dummy = new THREE.Object3D()
    const pVec = new THREE.Vector3()
    const tVec = new THREE.Vector3()

    function updateBelt(t) {
        for (let i = 0; i < beltCount; i++) {
            const u = (i / beltCount + t * 0.012) % 1
            path.getPointAt(u, pVec)
            path.getTangentAt(u, tVec)
            dummy.position.copy(pVec)
            dummy.rotation.set(0, -Math.atan2(tVec.z, tVec.x), 0)
            dummy.updateMatrix()
            beltSlats.setMatrixAt(i, dummy.matrix)
        }
        beltSlats.instanceMatrix.needsUpdate = true
    }
    updateBelt(0)

    // Rel pengaman kuningan di sisi conveyor
    for (const side of [-1, 1]) {
        const pts = []
        for (let i = 0; i <= 160; i++) {
            path.getPointAt(i / 160, pVec)
            path.getTangentAt(i / 160, tVec)
            pts.push(
                pVec.clone().add(new THREE.Vector3(-tVec.z * 0.36 * side, 0.09, tVec.x * 0.36 * side))
            )
        }
        const railPath = new THREE.CatmullRomCurve3(pts)
        belt.add(new THREE.Mesh(new THREE.TubeGeometry(railPath, 160, 0.028, 6, false), M.brass))
    }

    // Tiang penyangga rel
    for (let i = 0; i < 16; i++) {
        const p = path.getPointAt(i / 16)
        cyl(belt, 0.045, 0.43, p.x, 0.53, p.z, M.brass)
    }

    // Pipa kuningan penghubung antar stasiun
    const pipes = new THREE.Group()
    machine.add(pipes)
    for (let i = 0; i < 4; i++) {
        const a = stations[i].base
        const b = stations[i + 1].base
        const points = [
            a.clone().add(new THREE.Vector3(0.4, 0.12, 0)),
            a.clone().lerp(b, 0.35).add(new THREE.Vector3(0, 0.1, -0.6)),
            a.clone().lerp(b, 0.65).add(new THREE.Vector3(0, 0.1, -0.6)),
            b.clone().add(new THREE.Vector3(-0.4, 0.12, 0)),
        ]
        const curve = new THREE.CatmullRomCurve3(points)
        pipes.add(new THREE.Mesh(new THREE.TubeGeometry(curve, 30, 0.054, 8, false), M.brass))
    }

    // -------------------------------------------------------------
    // PAKET PESANAN BERTRANSFORMASI (Cargo Transformation)
    // -------------------------------------------------------------
    const cargoTextures = [orderCardTex, blueprintTex, joineryScreen, finishingTex, labelTex]
    const packets = []

    for (let i = 0; i < 6; i++) {
        const g = new THREE.Group()
        g.userData.moving = true
        machine.add(g)

        // Tubuh paket
        box(g, 0.48, 0.72, 0.04, 0, 0, 0, M.paper, 0.02)
        const faces = cargoTextures.map((tex) => {
            const s = screen(g, 0.44, 0.68, 0, 0, 0.025, tex)
            s.material.side = THREE.DoubleSide
            s.visible = false
            return s
        })

        // Cincin halo penanda pesanan aktif
        const halo = new THREE.Mesh(
            new THREE.RingGeometry(0.4, 0.42, 40),
            new THREE.MeshBasicMaterial({
                color: 0xff9f43,
                transparent: true,
                opacity: 0.85,
                side: THREE.DoubleSide,
                depthWrite: false,
            })
        )
        halo.rotation.x = -Math.PI / 2
        halo.position.y = -0.37
        g.add(halo)
        halo.visible = false

        packets.push({ group: g, faces, halo, stage: -1, phase: 0 })
    }

    // Koordinat stasiun di conveyor
    function nearestPort(x, z) {
        let best = 0
        let dist = Infinity
        for (let i = 0; i < 300; i++) {
            const p = path.getPointAt(i / 300)
            const d = (p.x - x) ** 2 + (p.z - z) ** 2
            if (d < dist) {
                dist = d
                best = i / 300
            }
        }
        return best
    }

    const joineryU = nearestPort(-1.65, -0.7)
    const finishU = nearestPort(1.5, -0.7)
    const dispatchU = nearestPort(0.93, 2.65)

    // Gabungkan mesh statis agar performa super ringan (draw call berkurang 80%)
    function compact(group) {
        for (const child of [...group.children]) {
            if (child.isGroup) compact(child)
        }
        const buckets = new Map()
        for (const object of group.children) {
            if (!object.isMesh || object.isInstancedMesh || object.userData.moving || Array.isArray(object.material))
                continue
            const key = object.material.uuid
            if (!buckets.has(key)) buckets.set(key, [])
            buckets.get(key).push(object)
        }
        for (const list of buckets.values()) {
            if (list.length < 2) continue
            const geos = list.map((m) => {
                m.updateMatrix()
                const g = m.geometry.index ? m.geometry.toNonIndexed() : m.geometry.clone()
                return g.applyMatrix4(m.matrix)
            })
            const merged = mergeGeometries(geos, false)
            for (const geo of geos) geo.dispose()
            if (!merged) continue
            const mesh = new THREE.Mesh(merged, list[0].material)
            mesh.castShadow = list.some((x) => x.castShadow)
            mesh.receiveShadow = list.some((x) => x.receiveShadow)
            for (const m of list) group.remove(m)
            group.add(mesh)
        }
    }

    ;[incomingOrder, woodenCrate, sawBlade, sprayNozzle].forEach((g) => (g.userData.moving = true))
    compact(machine)

    stations.forEach((s) => {
        s.group.traverse((o) => {
            o.userData.station = s.id
        })
    })

    const pickables = stations.map((s) => s.group)

    // -------------------------------------------------------------
    // LOGIKA ANIMASI & MODE
    // -------------------------------------------------------------
    let mode = 'assembled'
    let cameraMode = 'overview'
    let playing = true
    let simTime = 0
    let spread = 0
    let selected = 'design'
    let hovered = null

    let width = frameWidth()
    let height = frameHeight()
    const checkMobile = () => width <= 768

    let lastInteraction = performance.now()
    let dragging = false
    let wasDragged = false
    let downX = 0
    let downY = 0

    let cameraAnimating = true
    let flightTime = 0
    let lastDraw = -1
    let visible = true

    const desiredPosition = new THREE.Vector3()
    const desiredTarget = new THREE.Vector3(0, 0.8, 0)
    const viewDirection = new THREE.Vector3(10.5, 10.8, 17).normalize()
    let baseDistance = 22

    function layoutCamera() {
        if (!frameWidth() || !frameHeight()) return
        width = frameWidth()
        height = frameHeight()
        renderer.setSize(width, height)
        camera.aspect = width / height

        const aspect = width / height
        const isMobile = checkMobile()

        // FOV sedikit lebih luas di smartphone agar objek 3D tidak terdorong terlalu jauh
        camera.fov = isMobile ? 38 : 34

        const horizontalFit =
            15.5 / (2 * Math.tan(THREE.MathUtils.degToRad(camera.fov / 2)) * aspect * (isMobile ? 1.05 : 0.72))
        const verticalFit =
            10.2 / (2 * Math.tan(THREE.MathUtils.degToRad(camera.fov / 2)) * (isMobile ? 0.8 : 0.74))

        baseDistance = Math.max(horizontalFit, verticalFit) * (isMobile ? 0.95 : 1)
        controls.maxDistance = Math.max(45, baseDistance * 1.6)
        camera.updateProjectionMatrix()
        setCameraGoal()
        cameraAnimating = true
    }

    function setCameraGoal() {
        const isMobile = checkMobile()
        const expand = mode === 'stations' ? 1.22 : 1
        if (cameraMode === 'station') {
            const s = stations.find((s) => s.id === selected) || stations[0]
            desiredTarget.copy(s.group.position).add(new THREE.Vector3(0, 1.2, 0))
            desiredPosition.copy(desiredTarget).addScaledVector(viewDirection, isMobile ? 7.5 : 12)
        } else {
            // Pada mobile, titik target diturunkan sedikit (y = 0.35) agar diorama berada tepat di tengah area bebas kontrol
            desiredTarget.set(0, isMobile ? 0.35 : 0.8, 0)
            const distance = baseDistance * expand
            if (cameraMode === 'side') {
                desiredPosition.set(13, 5, 20).normalize().multiplyScalar(distance).add(desiredTarget)
            } else if (cameraMode === 'top') {
                desiredPosition.set(0.01, distance, 0.8).add(desiredTarget)
            } else {
                desiredPosition.copy(viewDirection).multiplyScalar(distance).add(desiredTarget)
            }
        }
    }

    function setMode(name) {
        mode = name
        lastInteraction = performance.now()
        if (mode === 'order') {
            simTime = 0
            lastDraw = -1
            playing = true
            cameraMode = 'overview'
        } else if (cameraMode === 'station') {
            cameraMode = 'overview'
        }
        setCameraGoal()
        cameraAnimating = true
    }

    function focusStation(id) {
        const s = stations.find((s) => s.id === id)
        if (!s) return
        selected = id
        if (mode === 'order') mode = 'assembled'
        cameraMode = 'station'
        lastInteraction = performance.now()
        setCameraGoal()
        cameraAnimating = true
    }

    function setCamera(name) {
        cameraMode = name
        if (mode === 'order') mode = 'assembled'
        lastInteraction = performance.now()
        flightTime = 0
        setCameraGoal()
        cameraAnimating = true
    }

    function play() {
        playing = true
    }

    function pause() {
        playing = false
    }

    // Simpan API global untuk Alpine.js
    window.__bewoleMachine = {
        setMode,
        focusStation,
        setCamera,
        play,
        pause,
    }

    layoutCamera()
    camera.position.copy(desiredPosition)
    controls.target.copy(desiredTarget)
    controls.update()
    cameraAnimating = false

    listen(window, 'resize', layoutCamera)
    const resizeObserver = new ResizeObserver(() => layoutCamera())
    resizeObserver.observe(root)
    cleanups.push(() => resizeObserver.disconnect())

    controls.addEventListener('start', () => {
        dragging = true
        cameraAnimating = false
        lastInteraction = performance.now()
    })
    controls.addEventListener('end', () => {
        dragging = false
        lastInteraction = performance.now()
    })
    controls.addEventListener('change', () => {
        if (dragging) lastInteraction = performance.now()
    })

    // Raycaster untuk tooltip stasiun
    const raycaster = new THREE.Raycaster()
    const pointer = new THREE.Vector2()
    const tooltip = $('.bewole-tooltip')

    function hitStation(x, y) {
        pointer.set((x / width) * 2 - 1, (-y / height) * 2 + 1)
        raycaster.setFromCamera(pointer, camera)
        const hits = raycaster.intersectObjects(pickables, true)
        return hits.length ? stations.find((s) => s.id === hits[0].object.userData.station) : null
    }

    listen(renderer.domElement, 'pointermove', (e) => {
        const rect = root.getBoundingClientRect()
        const clientX = e.clientX - rect.left
        const clientY = e.clientY - rect.top

        if (Math.hypot(clientX - downX, clientY - downY) > 5) wasDragged = true
        if (dragging) return

        const s = hitStation(clientX, clientY)
        hovered = s ? s.id : null
        renderer.domElement.style.cursor = s ? 'pointer' : 'grab'

        if (tooltip) {
            tooltip.classList.toggle('visible', !!s)
            if (s) {
                const titleEl = tooltip.querySelector('strong')
                const descEl = tooltip.querySelector('p')
                if (titleEl) {
                    titleEl.innerHTML = `<span>${String(s.index + 1).padStart(2, '0')}</span>${s.name}`
                }
                if (descEl) {
                    descEl.textContent = s.desc
                }
                tooltip.style.left = Math.min(width - 250, Math.max(10, clientX + 16)) + 'px'
                tooltip.style.top = Math.max(10, Math.min(height - 95, clientY - 65)) + 'px'
            }
        }
    })

    listen(renderer.domElement, 'pointerleave', () => {
        hovered = null
        if (tooltip) tooltip.classList.remove('visible')
    })

    listen(renderer.domElement, 'pointerdown', (e) => {
        const rect = root.getBoundingClientRect()
        downX = e.clientX - rect.left
        downY = e.clientY - rect.top
        wasDragged = false
        if (tooltip) tooltip.classList.remove('visible')
    })

    listen(renderer.domElement, 'pointerup', (e) => {
        if (wasDragged) return
        const rect = root.getBoundingClientRect()
        const s = hitStation(e.clientX - rect.left, e.clientY - rect.top)
        if (!s) return
        focusStation(s.id)
        options.onStation?.(s.id)
    })

    const journeySteps = [
        ['Menerima Request Desain', 'Studio Desain · Menganalisis sketsa & spesifikasi custom Anda'],
        ['Seleksi Kayu Jati Perhutani', 'Milling · Pemilihan jati Grade A kering oven & pemotongan presisi'],
        ['Konstruksi Sambungan Purus', 'Workshop · Perakitan sambungan purus & pasak tradisional oleh tukang Jepara'],
        ['Finishing & Lapisan Satin', 'Coating Lab · Pengamplasan halus & top coat PU matte anti-gores'],
        ['QC Passed & Siap Kirim', 'Ekspedisi · Peti kayu palet tersegel aman, siap dikirim ke rumah Anda'],
    ]

    let lastFrame = performance.now()
    let rafId = 0
    const scratch = new THREE.Vector3()
    const anchor = new THREE.Vector3()

    function animate(now) {
        rafId = requestAnimationFrame(animate)
        const dt = Math.max(0, Math.min((now - lastFrame) / 1000, 0.045))
        lastFrame = now

        if (!visible) return

        if (playing) {
            simTime += dt
            flightTime += dt
        }

        const t = simTime
        const tact = (t * TAU) / 4
        const smooth = 1 - Math.exp(-dt * 5)

        spread = THREE.MathUtils.lerp(spread, mode === 'stations' ? 1 : 0, smooth)
        cutPlane.constant = THREE.MathUtils.lerp(
            cutPlane.constant,
            mode === 'cutaway' ? 0.99 : 10,
            smooth
        )

        // Animasi per stasiun
        stations.forEach((s, i) => {
            s.group.position.copy(s.base)
            s.group.position.x *= 1 + spread * 0.29
            s.group.position.z *= 1 + spread * 0.37
            s.group.position.y += spread * (i % 2 ? 0.32 : 0.55)

            const pulse = Math.pow(Math.max(0, Math.sin(tact - i * 0.9)), 7)
            s.glowMat.emissiveIntensity = THREE.MathUtils.lerp(
                s.glowMat.emissiveIntensity,
                hovered === s.id || (cameraMode === 'station' && selected === s.id)
                    ? 3.2
                    : 0.55 + pulse * 0.6,
                smooth
            )
        })

        belt.scale.set(1 + spread * 0.12, 1, 1 + spread * 0.15)
        pipes.scale.set(1 + spread * 0.25, 1, 1 + spread * 0.3)

        // Putaran bilah gergaji mesin
        if (sawBlade.userData.bladeDisc) {
            sawBlade.userData.bladeDisc.rotation.z += t * 0.2
        }
        sawBlade.position.x = Math.sin(tact) * 0.4
        sprayNozzle.position.y = 0.81 + Math.sin(tact * 2) * 0.06

        if (t - lastDraw > 1 / 20 || lastDraw < 0) {
            updateBelt(t)
            lastDraw = t
        }

        // Pergerakan kargo pesanan di atas conveyor
        packets.forEach((packet, i) => {
            const phase = (t / 24 + i / 6) % 1
            packet.phase = phase
            let stage

            if (phase < 0.15) {
                stage = 0
                const f = phase / 0.15
                packet.group.position.copy(stations[0].group.position).add(new THREE.Vector3(0, 1.6, 0.6))
                scratch.copy(stations[1].group.position).add(new THREE.Vector3(0, 1.3, 0.65))
                packet.group.position.lerp(scratch, f)
                packet.group.position.y += Math.sin(f * Math.PI) * 2
                packet.group.rotation.set(0, Math.sin(f * Math.PI) * 0.28, Math.sin(f * Math.PI) * -0.1)
            } else {
                const u = ((phase - 0.15) / 0.85) * dispatchU
                path.getPointAt(u, packet.group.position)
                packet.group.position.x *= 1 + spread * 0.12
                packet.group.position.z *= 1 + spread * 0.15
                packet.group.position.y += 0.43
                packet.group.rotation.set(0, 0.18, 0)
                stage = u < joineryU * 0.7 ? 1 : u < finishU * 0.96 ? 2 : u < dispatchU * 0.83 ? 3 : 4
            }

            if (packet.stage !== stage) {
                packet.faces.forEach((f, j) => (f.visible = j === stage))
                packet.stage = stage
            }

            packet.group.visible = mode !== 'order' || i === 0
            packet.halo.visible = mode === 'order' && i === 0
            packet.group.scale.setScalar(mode === 'order' ? 1.35 : 1)
        })

        // Mode: One Order (Kamera melacak 1 pesanan dari tahap 0 s/d 4)
        if (mode === 'order') {
            const packet = packets[0]
            const step = Math.min(Math.max(packet.stage, 0), 4)
            const info = journeySteps[step] || journeySteps[0]

            options.onJourneyUpdate?.(step, info[0], info[1], Math.round(packet.phase * 100) + '%')

            if (!dragging && playing) {
                desiredTarget.copy(packet.group.position)
                desiredPosition.copy(desiredTarget).addScaledVector(viewDirection, checkMobile() ? 8 : 15)
                cameraAnimating = true
            }
        } else if (cameraMode === 'flight' && playing && !dragging) {
            const a = flightTime * 0.12
            const d = baseDistance * (mode === 'stations' ? 1.2 : 1)
            desiredTarget.set(0, 1, 0)
            desiredPosition
                .set(
                    Math.sin(a + 0.55) * d * 0.83,
                    d * (0.5 + Math.sin(a * 0.7) * 0.09),
                    Math.cos(a + 0.55) * d * 0.83
                )
                .add(desiredTarget)
            cameraAnimating = true
        } else if (cameraMode === 'station' && cameraAnimating) {
            setCameraGoal()
        }

        if (cameraAnimating && !dragging) {
            const speed = 1 - Math.exp(-dt * (mode === 'order' ? 2.2 : 3))
            camera.position.lerp(desiredPosition, speed)
            controls.target.lerp(desiredTarget, speed)
            if (
                cameraMode !== 'flight' &&
                mode !== 'order' &&
                camera.position.distanceTo(desiredPosition) < 0.015 &&
                controls.target.distanceTo(desiredTarget) < 0.015
            ) {
                cameraAnimating = false
            }
        }

        controls.autoRotate =
            playing &&
            !dragging &&
            !cameraAnimating &&
            mode !== 'order' &&
            cameraMode === 'overview' &&
            now - lastInteraction > 6500
        controls.autoRotateSpeed = 0.25
        controls.update(dt)

        // Posisi floating label saat mode 'stations'
        const isMobile = checkMobile()
        stations.forEach((s, i) => {
            if (!s.label) return
            const show = mode === 'stations'
            s.label.classList.toggle('visible', show)
            if (!show) return

            anchor.copy(s.group.position).add(s.anchor).project(camera)
            const offsets = isMobile
                ? [
                    [-30, 20],
                    [-20, -45],
                    [15, -45],
                    [15, 10],
                    [-10, 25],
                  ]
                : [
                    [-40, -14],
                    [-8, -64],
                    [25, -12],
                    [25, 20],
                    [-12, 35],
                  ]
            const x = THREE.MathUtils.clamp(
                (anchor.x * 0.5 + 0.5) * width - 40 + offsets[i][0],
                8,
                width - (isMobile ? 120 : 150)
            )
            const y = THREE.MathUtils.clamp(
                (-anchor.y * 0.5 + 0.5) * height - 52 + offsets[i][1],
                isMobile ? 55 : 80,
                height - (isMobile ? 85 : 120)
            )
            s.label.style.transform = `translate(${x}px,${y}px)`
        })

        renderer.render(scene, camera)
    }

    rafId = requestAnimationFrame(animate)

    cleanups.push(() => {
        cancelAnimationFrame(rafId)
        controls.dispose()

        scene.traverse((object) => {
            if (object.geometry) object.geometry.dispose()
            const list = object.material
                ? Array.isArray(object.material)
                    ? object.material
                    : [object.material]
                : []
            for (const m of list) {
                for (const v of Object.values(m)) {
                    if (v instanceof THREE.Texture) v.dispose()
                }
                m.dispose()
            }
        })
        geometries.forEach((g) => g.dispose())
        env.dispose()
        renderer.dispose()
        renderer.forceContextLoss()
        renderer.domElement.remove()
        delete window.__bewoleMachine
    })

    return () => {
        for (const fn of cleanups.reverse()) fn()
        cleanups.length = 0
    }
}
