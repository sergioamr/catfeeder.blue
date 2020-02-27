import os

import numpy as np
import imageio

from skimage import data, color
from skimage.transform import rescale, resize, downscale_local_mean

xs = np.arange(8*8*3).reshape(8,8,3)

print xs
print "---------------------"

x = xs[:, :, 0]

#print x

v = np.vstack(xs)
print v.shape
print v

x = xs.reshape(4,16,3)

print x.shape
print x

#rescaled_image = 255 * v
im_resized = x.astype(np.uint8)
imageio.imwrite("test_shape.png", im_resized)

#x = numpy.dstack((xs[:, :1024], xs[:, 1024:2048], xs[:, 2048:]))

#for i in range(50):
#  imsave('fr_batch_'+`i`+'.png', x[1000*i:1000*(i+1),:])

#y = numpy.concatenate(ys)



#imsave('cifar10_batch_'+`50`+'.png', x[50000:51000,:]) # test set

# dump the labels
#L = 'var labels=' + `list(y[:51000])` + ';\n'
#open('classifier/fr_labels.js', 'w').write(L)

